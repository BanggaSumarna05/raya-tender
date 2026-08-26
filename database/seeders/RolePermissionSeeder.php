<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Dashboard
            'view_dashboard',

            // Tender
            'view_tenders',
            'create_tenders',
            'update_tenders',
            'delete_tenders',
            'export_tenders',
            'change_tender_status',  // NEW: explicit permission for status changes

            // Proposal
            'view_proposals',
            'create_proposals',
            'update_proposals',
            'delete_proposals',
            'export_proposals',
            'create_proposal_revisions', // NEW: creating new revision/version

            // Financial data — sensitive, requires explicit grant
            'view_financial_data',   // NEW: view tender_value, bid_value, contract_value etc.

            // Document
            'view_documents',
            'upload_documents',
            'download_documents',
            'delete_documents',

            // Client
            'view_clients',
            'create_clients',
            'update_clients',
            'delete_clients',

            // Category
            'view_categories',
            'create_categories',
            'update_categories',
            'delete_categories',

            // Reports
            'view_reports',
            'export_reports',

            // Users
            'view_users',
            'create_users',
            'update_users',
            'delete_users',

            // Activity Log
            'view_activity_logs',

            // Reminders
            'view_reminders',
            'create_reminders',
            'update_reminders',
            'delete_reminders',

            // Settings
            'view_settings',
            'update_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----------------------------------------------------------------
        // ROLES
        // ----------------------------------------------------------------

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Marketing Manager — full operational access + financial data
        $manager = Role::firstOrCreate(['name' => 'Marketing Manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view_dashboard',
            'view_tenders', 'create_tenders', 'update_tenders', 'delete_tenders', 'export_tenders',
            'change_tender_status',
            'view_proposals', 'create_proposals', 'update_proposals', 'delete_proposals', 'export_proposals',
            'create_proposal_revisions',
            'view_financial_data',                    // Manager can see financial data
            'view_documents', 'upload_documents', 'download_documents', 'delete_documents',
            'view_clients', 'create_clients', 'update_clients', 'delete_clients',
            'view_categories',
            'view_reports', 'export_reports',
            'view_activity_logs',
            'view_reminders', 'create_reminders', 'update_reminders', 'delete_reminders',
        ]);

        // Marketing Staff — limited access, NO financial data by default
        $staff = Role::firstOrCreate(['name' => 'Marketing Staff', 'guard_name' => 'web']);
        $staff->givePermissionTo([
            'view_dashboard',
            'view_tenders', 'create_tenders', 'update_tenders',
            'change_tender_status',
            'view_proposals', 'create_proposals', 'update_proposals',
            'create_proposal_revisions',
            // view_financial_data NOT granted — staff cannot see financial values by default
            'view_documents', 'upload_documents', 'download_documents',
            'view_clients', 'create_clients', 'update_clients',
            'view_categories',
            'view_reports',
            'view_reminders', 'create_reminders', 'update_reminders',
        ]);
    }
}
