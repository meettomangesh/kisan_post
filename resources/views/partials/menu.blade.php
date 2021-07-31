<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a href="{{ route("admin.home") }}" class="nav-link">
                    <i class="nav-icon fas fa-fw fa-tachometer-alt">

                    </i>
                    {{ trans('global.dashboard') }}
                </a>
            </li>

            @can('user_management_access')
            <li class="nav-item nav-dropdown">
                <a class="nav-link  nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="nav-dropdown-items">
                    @can('permission_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-unlock-alt nav-icon">

                            </i>
                            {{ trans('cruds.permission.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('role_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-briefcase nav-icon">

                            </i>
                            {{ trans('cruds.role.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('user_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-user nav-icon">

                            </i>
                            {{ trans('cruds.user.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('deliveryboy_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.deliveryboys.index") }}" class="nav-link {{ request()->is('admin/deliveryboys') || request()->is('admin/deliveryboys/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon">

                            </i>
                            {{ trans('cruds.deliveryboy.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('customers_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.customers.index") }}" class="nav-link {{ request()->is('admin/customers') || request()->is('admin/customers/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.customers.title') }}
                        </a>
                    </li>
                    @endcan
                   
                </ul>
            </li>
            @endcan

            @can('territories_access')
            <li class="nav-item nav-dropdown">
                <a class="nav-link  nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users nav-icon">

                    </i>
                    {{ trans('cruds.territories.title') }}
                </a>
                <ul class="nav-dropdown-items">
                    @can('country_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.countries.index") }}" class="nav-link {{ request()->is('admin/countries') || request()->is('admin/countries/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-flag nav-icon">

                            </i>
                            {{ trans('cruds.country.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('state_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.states.index") }}" class="nav-link {{ request()->is('admin/states') || request()->is('admin/states/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-flag nav-icon">

                            </i>
                            {{ trans('cruds.state.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('city_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.cities.index") }}" class="nav-link {{ request()->is('admin/cities') || request()->is('admin/cities/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon">

                            </i>
                            {{ trans('cruds.city.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('pin_code_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.pincodes.index") }}" class="nav-link {{ request()->is('admin/pincodes') || request()->is('admin/pincodes/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon">

                            </i>
                            {{ trans('cruds.pin_code.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('region_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.regions.index") }}" class="nav-link {{ request()->is('admin/regions') || request()->is('admin/regions/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon">

                            </i>
                            {{ trans('cruds.region.title') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            <li>
            @endcan
            
            @can('product_management_access')
            <li class="nav-item nav-dropdown">
                <a class="nav-link  nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users nav-icon">

                    </i>
                    {{ trans('cruds.productManagement.title') }}
                </a>
                <ul class="nav-dropdown-items">
                    @can('category_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.categories.index") }}" class="nav-link {{ request()->is('admin/categories') || request()->is('admin/categories/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.category.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('unit_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.units.index") }}" class="nav-link {{ request()->is('admin/units') || request()->is('admin/units/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.unit.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('product_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.products.index") }}" class="nav-link {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.product.title_master') }}
                        </a>
                    </li>
                    @endcan
                    @can('product_unit_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.product_units.index") }}" class="nav-link {{ request()->is('admin/product_units') || request()->is('admin/product_units/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.product_unit.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('basket_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.baskets.index") }}" class="nav-link {{ request()->is('admin/baskets') || request()->is('admin/baskets/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.basket.title') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            <li>
            @endcan

            @can('order_management_access')
            <li class="nav-item nav-dropdown">
                <a class="nav-link  nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users nav-icon"></i>
                    {{ trans('cruds.orderManagement.title') }}
                </a>
                <ul class="nav-dropdown-items">
                    @can('order_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.orders.index") }}" class="nav-link {{ request()->is('admin/orders') || request()->is('admin/orders/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.order.title') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            <li>
            @endcan

            @can('banner_access')
            <li class="nav-item">
                <a href="{{ route("admin.banners.index") }}" class="nav-link {{ request()->is('admin/banners') || request()->is('admin/banners/*') ? 'active' : '' }}">
                    <i class="fa-fw fas fa-cogs nav-icon"></i>
                    {{ trans('cruds.banner.title') }}
                </a>
            </li>
            @endcan

            @can('communication_access')
            <li class="nav-item">
                <a href="{{ route("admin.communications.index") }}" class="nav-link {{ request()->is('admin/communications') || request()->is('admin/communications/*') ? 'active' : '' }}">
                    <i class="fa-fw fas fa-cogs nav-icon"></i>
                    {{ trans('cruds.communication.title') }}
                </a>
            </li>
            @endcan

            @can('campaign_access')
            <li class="nav-item">
                <a href="{{ route("admin.campaigns.index") }}" class="nav-link {{ request()->is('admin/campaigns') || request()->is('admin/campaigns/*') ? 'active' : '' }}">
                    <i class="fa-fw fas fa-cogs nav-icon"></i>
                    {{ trans('cruds.campaign.title') }}
                </a>
            </li>
            @endcan

            @can('report_access')
            <li class="nav-item nav-dropdown">
                <a class="nav-link  nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users nav-icon"></i>
                    {{ trans('cruds.report.title') }}
                </a>
                <ul class="nav-dropdown-items">
                    @can('report_sales_itemwise_access')
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.salesItemwise') }}" class="nav-link {{ request()->is('admin/reports') || request()->is('admin/reports/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.report.fields.sales_itemwise') }}
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ route("admin.reports.loginLogs") }}" class="nav-link {{ request()->is('admin/reports') || request()->is('admin/reports/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs nav-icon"></i>
                            {{ trans('cruds.loginlogs.title') }}
                        </a>
                    </li>
                </ul>
            <li>
            @endcan
            
            @can('purchase_form_access')
            <li class="nav-item">
                <a href="{{ route("admin.purchase_form.index") }}" class="nav-link {{ request()->is('admin/purchase_form') || request()->is('admin/purchase_form/*') ? 'active' : '' }}">
                    <i class="fa-fw fas fa-cogs nav-icon"></i>
                    {{ trans('cruds.purchase_form.title') }}
                </a>
            </li>
            @endcan

            @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
            <li class="nav-item">
                <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                    <i class="fa-fw fas fa-key nav-icon">
                    </i>
                    {{ trans('global.change_password') }}
                </a>
            </li>
            @endcan
            @endif
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>

    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>