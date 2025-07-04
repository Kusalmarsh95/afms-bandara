<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('images/user.png') }}" class="img-circle elevation-3" alt="User Image">
        </div>
        <div class="info">
            <a href="{{ route('profile.show') }}" class="d-block">{{ Auth::user()->name }}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
            data-accordion="false">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="nav-icon fas fa-th text-red"></i>
                    <p>
                        {{ __('Dashboard') }}
                    </p>
                </a>
            </li>

            @can('administration')
            <li class="nav-item has-treeview {{ request()->is('users*') || request()->is('roles*') ?  'menu-open' : '' }}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-edit text-green"></i>
                    <p> {{ __('Administration') }} <i class="fas fa-angle-left right text-green"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('administration-user')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link  {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa fa-user-circle text-green"></i>
                            <p> {{ __('Users') }} </p>
                        </a>
                    </li>
                    @endcan
                    @can('administration-role')
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <i class="nav-icon fas  fa-universal-access text-green"></i>
                            <p> {{ __('Roles') }} </p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan
            @can('master-data')
                <li class="nav-item {{ request()->is('units*') || request()->is('regiments*') || request()->is('ranks*') || request()->is('relationships*') || request()->is('districts*') || request()->is('banks*') || request()->is('bank-branches*') || request()->is('reject-reasons*') || request()->is('member-status*') || request()->is('withdrawal-products*') || request()->is('contribution-interests*') || request()->is('loan-products*') ?  'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-address-card text-blue"></i>
                        <p> {{ __('Master Data') }} <i class="fas fa-angle-left right text-blue"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('master-data-unit')
                        <li class="nav-item">
                            <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa fa-city text-blue"></i>
                                <p> {{ __('Units') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-regiment')
                        <li class="nav-item">
                            <a href="{{ route('regiments.index') }}" class="nav-link {{ request()->routeIs('regiments.*') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-place-of-worship text-blue"></i>
                                <p> {{ __('Regiments') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-rank')
                        <li class="nav-item">
                            <a href="{{ route('ranks.index') }}" class="nav-link {{ request()->routeIs('ranks.*') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-star text-blue"></i>
                                <p> {{ __('Ranks') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-relashionship')
                        <li class="nav-item">
                            <a href="{{ route('relationships.index') }}" class="nav-link {{ request()->routeIs('relationships.*') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-user-friends text-blue"></i>
                                <p> {{ __('Relationships') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-district')
                        <li class="nav-item">
                            <a href="{{ route('districts.index') }}" class="nav-link {{ request()->routeIs('districts.*') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-map-location-dot text-blue"></i>
                                <p> {{ __('Districts') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-bank')
                        <li class="nav-item">
                            <a href="{{ route('banks.index') }}" class="nav-link {{ request()->routeIs('banks.*') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-building-columns text-blue"></i>
                                <p> {{ __('Banks') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-bank-branch')
                        <li class="nav-item">
                            <a href="{{ route('bank-branches.index') }}" class="nav-link {{ request()->routeIs('bank-branches.*') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-braille text-blue"></i>
                                <p> {{ __('Bank Branches') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-reject-reason')
                        <li class="nav-item">
                            <a href="{{ route('reject-reasons.index') }}" class="nav-link {{ request()->routeIs('reject-reasons.*') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-cogs text-blue"></i>
                                <p> {{ __('Reject Reason') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-member-status')
                        <li class="nav-item">
                            <a href="{{ route('member-status.index') }}" class="nav-link {{ request()->routeIs('member-status.*') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-user-plus text-blue"></i>
                                <p> {{ __('Member Status') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-withdrawal-product')
                        <li class="nav-item">
                            <a href="{{ route('withdrawal-products.index') }}" class="nav-link {{ request()->routeIs('withdrawal-products.*') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-calculator text-blue"></i>
                                <p> {{ __('Withdrawal Product') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-contribution-interest')
                        <li class="nav-item">
                            <a href="{{ route('contribution-interests.index') }}" class="nav-link {{ request()->routeIs('contribution-interests.*') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-list-ol text-blue"></i>
                                <p> {{ __('Contribution Interest') }} </p>
                            </a>
                        </li>
                        @endcan
                        @can('master-data-loan-product')
                        <li class="nav-item">
                            <a href="{{ route('loan-products.index') }}" class="nav-link {{ request()->routeIs('loan-products.*') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-spinner text-blue"></i>
                                <p> {{ __('Loan Product') }} </p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @can('memberships')
            <li class="nav-item {{request()->is('memberships*') || request()->is('membership-assigns*') || request()->is('membership-changes*') || request()->is('membership-rejects*') || request()->is('membership-approval*') ? 'menu-open' : ''}}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users text-yellow"></i>
                    <p> {{ __('Memberships') }} <i class="fas fa-angle-left right text-yellow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('memberships-registered')
                    <li class="nav-item">
                        <a href="{{ route('memberships.index') }}" class="nav-link {{request()->routeIs('memberships.*') || request()->routeIs('membership-approval*')  ? 'active' : ''}}">
                            <i class="nav-icon fas fa-user-shield text-yellow"></i>
                            <p>
                                {{ __('Registered Members') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('memberships-new')
                    <li class="nav-item">
                        <a href="{{ route('membership-assigns') }}" class="nav-link {{request()->routeIs('membership-assigns') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-user-clock text-yellow"></i>
                            <p> {{ __('New Members') }} </p>
                        </a>
                    </li>
                    @endcan
                    @can('memberships-changes')
                    <li class="nav-item">
                        <a href="{{ route('membership-changes') }}" class="nav-link {{request()->routeIs('membership-changes') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-user-cog text-yellow"></i>
                            <p> {{ __('Member Changes') }} </p>
                        </a>
                    </li>
                    @endcan
                    @can('memberships-rejected')
                    <li class="nav-item">
                        <a href="{{ route('membership-rejects') }}" class="nav-link {{request()->routeIs('membership-rejects') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-user-times text-yellow"></i>
                            <p> {{ __('Rejected Members') }} </p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            @can('nominees')
            <li class="nav-item {{request()->is('nominees*') || request()->is('nominee-changes*') || request()->is('nominee-rejects*') || request()->is('nominee-approval*') ? 'menu-open' : ''}}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-child text-teal"></i>
                    <p> {{ __('Nominees') }} <i class="fas fa-angle-left right text-teal"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('nominees-new')
                    <li class="nav-item">
                        <a href="{{ route('nominees.newNominees') }}" class="nav-link  {{request()->routeIs('nominees*') || request()->routeIs('nominee-approval*') ? 'active' : ''}}">
                            <i class="nav-icon fas fa-person-booth text-teal"></i>
                            <p>
                                {{ __('New Nominees') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('nominees-changes')
                    <li class="nav-item">
                        <a href="{{ route('nominee-changes') }}" class="nav-link  {{request()->routeIs('nominee-changes*') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-people-arrows text-teal"></i>
                            <p> {{ __('Nominee Changes') }} </p>
                        </a>
                    </li>
                    @endcan
                    @can('nominees-rejected')
                    <li class="nav-item">
                        <a href="{{ route('nominee-rejects') }}" class="nav-link  {{request()->routeIs('nominee-rejects*') ? 'active' : ''}}">
                            <i class="nav-icon fa fa-window-close text-teal"></i>
                            <p> {{ __('Rejected Nominees') }} </p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            @can('bulk')
            <li class="nav-item {{request()->is('monthlyDeductions*') || request()->is('additional-contribution*') || request()->is('create-calculation*') || request()->is('contribution-upload*') || request()->is('repayment-upload*') || request()->is('interest-calculation*') ? 'menu-open' : ''}}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-upload text-purple"></i>
                    <p> {{ __('Bulk Management') }} <i class="fas fa-angle-left right text-purple"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('bulk-deduction')
                    <li class="nav-item">
                        <a href="{{ route('monthlyDeductions.index') }}" class="nav-link  {{request()->routeIs('monthlyDeductions.*') || request()->routeIs('contribution-upload*') || request()->routeIs('repayment-upload*') ? 'active' : ''}}">
                            <i class="nav-icon fas fa fa-money-check text-purple"></i>
                            <p> {{ __('Monthly Bulk Uploads') }} </p>
                        </a>
                    </li>
                    @endcan
                    @can('bulk-interest-calculation')
                    <li class="nav-item">
                        <a href="{{ route('create-calculation') }}" class="nav-link  {{request()->Is('interest-calculation*') ? 'active' : ''}}">
                            <i class="nav-icon fas fa fa-money-bill text-purple"></i>
                            <p> {{ __('Interest Calculation') }} </p>
                        </a>
                    </li>
                    @endcan
                </ul>

            </li>
            @endcan

            @can('withdrawals')
            <li class="nav-item {{request()->is('partial-withdrawals*') || request()->is('full-withdrawals*') || request()->is('full-approved*') ? 'menu-open' : ''}}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-money-bill-wave text-cyan"></i>
                    <p> {{ __('Withdrawals') }} <i class="fas fa-angle-left right text-cyan"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('withdrawals-partial')
                    <li class="nav-item">
                        <a href="{{ route('withdrawals.indexPartial') }}" class="nav-link  {{request()->is('partial-withdrawals*') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-money-check-alt text-cyan"></i>
                            <p>
                                {{ __('Partial Withdrawals') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('withdrawals-partial')
                    <li class="nav-item">
                        <a href="{{ route('partial.bulk') }}" class="nav-link  {{request()->is('partial-bulk*') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-money-check-alt text-cyan"></i>
                            <p>
                                {{ __('Partial Withdrawal Bulk') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('withdrawals-full')
                    <li class="nav-item">
                        <a href="{{ route('withdrawals.indexFull') }}" class="nav-link  {{request()->is('full-withdrawals*') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-money-bill-alt text-cyan"></i>
                            <p>
                                {{ __('Full Withdrawals') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('withdrawals-full')
                        <li class="nav-item">
                            <a href="{{ route('full.bulk') }}" class="nav-link  {{request()->is('full-bulk*') ? 'active' : ''}}">
                                <i class="nav-icon fas  fa-money-check-alt text-cyan"></i>
                                <p>
                                    {{ __('Full Withdrawal Bulk') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                </ul>

            </li>
            @endcan

            @can('loans')
            <li class="nav-item {{request()->is('loan*') || request()->is('loan-settlement') ? 'menu-open' : ''}}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-university text-orange"></i>
                    <p> {{ __('Loans') }} <i class="fas fa-angle-left right text-orange"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('loans-applications')
                    <li class="nav-item">
                        <a href="{{ route('loan.index') }}" class="nav-link {{request()->is('loan') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-money-check-alt text-orange"></i>
                            <p>
                                {{ __('Loan Applications') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('loans-applications')
                        <li class="nav-item">
                            <a href="{{ route('loan.bulk') }}" class="nav-link  {{request()->routeIs('monthlyDeductions.*') || request()->routeIs('contribution-upload*') || request()->routeIs('repayment-upload*') ? 'active' : ''}}">
                                <i class="nav-icon fas fa fa-money-check text-purple"></i>
                                <p> {{ __('Loan Bulk') }} </p>
                            </a>
                        </li>
                    @endcan

                    @can('loans-direct-settlement')
                    <li class="nav-item">
                        <a href="{{ route('loan.indexSettlement') }}" class="nav-link  {{request()->is('loan-settlement') ? 'active' : ''}}">
                            <i class="nav-icon fas  fa-money-check-alt text-orange"></i>
                            <p>
                                {{ __('Settlement Applications') }}
                            </p>
                        </a>
                    </li>
                    @endcan
                    @can('loans-applications')
                        <li class="nav-item">
                            <a href="{{ route('absent-settlement') }}" class="nav-link {{request()->is('force-settlement') ? 'active' : ''}}">
                                <i class="nav-icon fas  fa-money-check-alt text-orange"></i>
                                <p>
                                    {{ __('Absent Settlements') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('loans-applications')
                        <li class="nav-item">
                            <a href="{{ route('suwasahana.index') }}" class="nav-link {{request()->is('suwasahana') ? 'active' : ''}}">
                                <i class="nav-icon fas  fa-money-check-alt text-orange"></i>
                                <p>
                                    {{ __('New Suwasahana Loans') }}
                                </p>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
            @endcan
            @can('bulk')
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-download text-white"></i>
                        <p> {{ __('Reports') }} </p>
                    </a>
                </li>
            @endcan

            @can('master-data')
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-book-atlas text-blue"></i>
                    <p> {{ __('Audit') }} <i class="fas fa-angle-left right text-blue"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('bulk-additional-contribution')
                        <li class="nav-item">
                            <a href="{{ route('additional-contribution') }}" class="nav-link  {{request()->routeIs('additional-contribution*') ? 'active' : ''}}">
                                <i class="nav-icon fas fa fa-money-bill text-blue"></i>
                                <p> {{ __('Additional Contributions') }} </p>
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ route('corrections') }}" class="nav-link">
                            <i class="nav-icon fas  fa-money-check-alt text-blue"></i>
                            <p>
                                {{ __('Contribution Corrections') }}
                            </p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="dropdown-item"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="mr-2 fas fa-sign-out-alt text-pink"></i>
                        {{ __('Log Out') }}
                    </a>
                </form>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
