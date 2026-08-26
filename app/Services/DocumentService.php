<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DocumentService
{
    /**
     * Peta MIME type yang diizinkan beserta ekstensi canonicalnya.
     * Validasi berdasarkan MIME aktual file (bukan ekstensi nama file dari client).
     */
    private const ALLOWED_MIME_MAP = [
        'application/pdf'                                                          => 'pdf',
        'application/msword'                                                       => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'  => 'docx',
        'application/vnd.ms-excel'                                                 => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'        => 'xlsx',
        'application/vnd.ms-powerpoint'                                            => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'=> 'pptx',
        'image/jpeg'                                                               => 'jpg',
        'image/png'                                                                => 'png',
        'image/gif'                                                                => 'gif',
        'application/zip'                                                          => 'zip',
        'application/x-zip-compressed'                                             => 'zip',
        'application/x-rar-compressed'                                             => 'rar',
        'application/octet-stream'                                                 => null, // handled separately
    ];

    /**
     * Ekstensi yang diizinkan (lowercase).
     */
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar',
    ];

    /**
     * Ekstensi yang secara mutlak dilarang, apapun MIME-nya.
     */
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'py', 'rb', 'pl', 'js',
        'html', 'htm', 'svg', 'xml', 'htaccess',
    ];

    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function store(array $data, UploadedFile $file): Document
    {
        // Validasi MIME dan ekstensi di service layer (defense in depth)
        $this->validateFile($file);

        return DB::transaction(function () use ($data, $file) {
            $path     = $this->storeFile($file);
            $fileName = basename($path);

            // Gunakan ekstensi canonical dari MIME map, bukan dari nama file client
            $actualMime      = $file->getMimeType();
            $safeExtension   = $this->resolveExtension($actualMime, $file->getClientOriginalExtension());

            $document = Document::create([
                'tender_id'     => $data['tender_id'] ?? null,
                'proposal_id'   => $data['proposal_id'] ?? null,
                'category_id'   => $data['category_id'] ?? null,
                'name'          => $data['name'],
                'original_name' => $this->sanitizeFilename($file->getClientOriginalName()),
                'file_path'     => $path,
                'file_name'     => $fileName,
                'mime_type'     => $actualMime,
                'file_size'     => $file->getSize(),
                'extension'     => $safeExtension,
                'description'   => $data['description'] ?? null,
                'uploaded_by'   => Auth::id(),
            ]);

            $this->activityLogService->log(
                'UPLOAD_DOCUMENT',
                'Document',
                $document->id,
                Document::class,
                "Mengunggah dokumen: {$document->name}",
                [
                    'file'      => $document->original_name,
                    'size'      => $document->file_size,
                    'mime'      => $actualMime,
                    'extension' => $safeExtension,
                ]
            );

            return $document;
        });
    }

    public function delete(Document $document): bool
    {
        return DB::transaction(function () use ($document) {
            $this->activityLogService->log(
                'DELETE_DOCUMENT',
                'Document',
                $document->id,
                Document::class,
                "Menghapus dokumen: {$document->name}"
            );

            if (Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            return $document->delete();
        });
    }

    public function download(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Pastikan file masih ada sebelum download
        if (!Storage::disk('private')->exists($document->file_path)) {
            throw new \RuntimeException("File tidak ditemukan di storage.");
        }

        $this->activityLogService->log(
            'DOWNLOAD_DOCUMENT',
            'Document',
            $document->id,
            Document::class,
            "Mengunduh dokumen: {$document->name}"
        );

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_name
        );
    }

    /**
     * Validasi file berdasarkan MIME aktual konten + ekstensi.
     * Ini adalah lapisan defense-in-depth selain validasi di Form Request.
     */
    private function validateFile(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType  = $file->getMimeType(); // Dibaca dari konten file, bukan header client

        // Blokir ekstensi berbahaya secara mutlak
        if (in_array($extension, self::BLOCKED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => "Tipe file '{$extension}' tidak diizinkan.",
            ]);
        }

        // Validasi ekstensi ada di whitelist
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => "Ekstensi file '.{$extension}' tidak didukung.",
            ]);
        }

        // Validasi MIME type aktual (dari konten file) ada di whitelist
        if (!array_key_exists($mimeType, self::ALLOWED_MIME_MAP)) {
            throw ValidationException::withMessages([
                'file' => "Tipe konten file '{$mimeType}' tidak diizinkan.",
            ]);
        }

        // Cek konsistensi MIME vs ekstensi (mencegah camouflage)
        // Contoh: file .jpg yang MIME-nya application/pdf → tolak
        $expectedExtension = self::ALLOWED_MIME_MAP[$mimeType];
        if ($expectedExtension !== null && $expectedExtension !== $extension) {
            // Izinkan jpg/jpeg keduanya
            $jpegAliases = ['jpg', 'jpeg'];
            $zipAliases  = ['zip'];

            $isJpegAlias = $expectedExtension === 'jpg' && in_array($extension, $jpegAliases, true);
            $isZipAlias  = $expectedExtension === 'zip' && in_array($extension, $zipAliases, true);

            if (!$isJpegAlias && !$isZipAlias) {
                throw ValidationException::withMessages([
                    'file' => "Konten file tidak sesuai dengan ekstensi '.{$extension}'. Kemungkinan file berbahaya.",
                ]);
            }
        }
    }

    /**
     * Simpan file dengan nama UUID — tidak pernah menggunakan nama asli dari client.
     */
    private function storeFile(UploadedFile $file): string
    {
        $year   = now()->year;
        $month  = now()->format('m');
        $folder = "documents/{$year}/{$month}";

        // Nama file sepenuhnya random UUID — tidak ada info dari user
        $safeName = Str::uuid()->toString() . '.' . strtolower($file->getClientOriginalExtension());

        return $file->storeAs($folder, $safeName, 'private');
    }

    /**
     * Tentukan ekstensi canonical berdasarkan MIME type aktual.
     */
    private function resolveExtension(string $mimeType, string $clientExtension): string
    {
        $canonical = self::ALLOWED_MIME_MAP[$mimeType] ?? null;

        if ($canonical !== null) {
            return $canonical === 'jpg' ? strtolower($clientExtension) : $canonical;
        }

        // Fallback ke ekstensi client jika sudah di-whitelist
        return strtolower($clientExtension);
    }

    /**
     * Sanitasi nama file untuk disimpan di DB — hanya tampilan, tidak dipakai untuk path.
     */
    private function sanitizeFilename(string $filename): string
    {
        // Hapus karakter berbahaya, normalkan spasi
        $safe = preg_replace('/[^\w\s.\-]/', '', $filename);
        $safe = preg_replace('/\s+/', ' ', trim($safe));

        return $safe ?: 'untitled';
    }
}
