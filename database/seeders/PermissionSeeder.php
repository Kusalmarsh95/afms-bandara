<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            
            'administration',

            'administration-user',
            'administration-user-create',
            'administration-user-edit',
            'administration-user-delete',

            'administration-role',
            'administration-role-create',
            'administration-role-edit',
            'administration-role-delete',

            'master-data',

            'master-data-unit',
            'master-data-unit-create',
            'master-data-unit-edit',
            'master-data-unit-delete',

            'master-data-regiment',
            'master-data-regiment-create',
            'master-data-regiment-edit',
            'master-data-regiment-delete',

            'master-data-rank',
            'master-data-rank-create',
            'master-data-rank-edit',
            'master-data-rank-delete',

            'master-data-relashionship',
            'master-data-relashionship-create',
            'master-data-relashionship-edit',
            'master-data-relashionship-delete',

            'master-data-district',
            'master-data-district-create',
            'master-data-district-edit',
            'master-data-district-delete',

            'master-data-bank',
            'master-data-bank-create',
            'master-data-bank-edit',
            'master-data-bank-delete',

            'master-data-bank-branch',
            'master-data-bank-branch-create',
            'master-data-bank-branch-edit',
            'master-data-bank-branch-delete',

            'master-data-reject-reason',
            'master-data-reject-reason-create',
            'master-data-reject-reason-edit',
            'master-data-reject-reason-delete',

            'master-data-member-status',
            'master-data-member-status-create',
            'master-data-member-status-edit',
            'master-data-member-status-delete',

            'master-data-withdrawal-product',
            'master-data-withdrawal-product-create',
            'master-data-withdrawal-product-edit',
            'master-data-withdrawal-product-delete',

            'master-data-contribution-interest',
            'master-data-contribution-interest-create',
            'master-data-contribution-interest-edit',
            'master-data-contribution-interest-delete',

            'master-data-loan-product',
            'master-data-loan-product-create',
            'master-data-loan-product-edit',
            'master-data-loan-product-delete',

            'memberships',
            'memberships-edit',
            'memberships-delete',

            'memberships-registered',
            'memberships-registered-show',
            'memberships-registered-absent-data-add',
            'memberships-registered-nominee-add',
            'memberships-registered-contribution-add',
            'memberships-registered-opening-balance-edit',
            'memberships-registered-withdrawal-partial-add',
            'memberships-registered-withdrawal-full-add',
            'memberships-registered-loan-add',
            'memberships-registered-loan-show',
            'memberships-registered-loan-edit',
            'memberships-registered-suwasahana-edit',

            'memberships-new',
            'memberships-approve',

            'memberships-changes',
            'memberships-changes-approve',

            'memberships-rejected',
            'memberships-rejected-approve',
            'memberships-rejected-reject',

            'nominees',
            'nominees-edit',
            'nominees-approve',
            'nominees-reject',
            'nominees-delete',

            'nominees-new',

            'nominees-changes',

            'nominees-rejected',

            'bulk',
            'bulk-deduction',
            'bulk-interest',
            'bulk-changes',
            'bulk-suwasahana-repayment',
            'bulk-loan-repayment',

            'bulk-additional-contribution',
            'bulk-additional-contribution-edit',
            'bulk-additional-contribution-approve',
            'bulk-additional-contribution-delete',
            
            'bulk-interest-calculation',

            'withdrawals',
            'withdrawals-partial',
            'withdrawals-full',

            'withdrawals-partial-edit',
            'withdrawals-partial-approve',

            'withdrawals-partial-disburse',
            
            'withdrawals-partial-delete',

            'withdrawals-full-edit',
            'withdrawals-full-approve',

            'withdrawals-full-disburse',

            'withdrawals-full-approve',
            'withdrawals-full-delete',

            'loans',
            
            'loans-applications',

            'loans-applications-show',
            'loans-applications-approved-show',

            'loans-applications-edit',
            'loans-applications-delete',

            'loans-applications-forward',
            'loans-applications-process',
            'loans-applications-approve',
            'loans-applications-to-disburse',
            'loans-applications-disburse',
            'loans-applications-reject',

            'loans-direct-settlement', 

            'loans-direct-settlement-show',
            'loans-direct-settlement-edit',
            'loans-direct-settlement-forward',
            'loans-direct-settlement-to-settle',
            'loans-direct-settlement-settle',
            'loans-direct-settlement-reject',
            'loans-direct-settlement-approve',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
