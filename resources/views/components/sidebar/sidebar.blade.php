<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">

            @if (auth()->user()->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle"
                    style="width:40px; height:40px; object-fit:cover;" alt="Avatar">
            @else
                <span class="badge bg-secondary rounded-circle"
                    style="width:40px; height:40px; display: inline-flex; align-items: center; justify-content: center;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            @endif
            <p class="user-name h5 mt-2 mb-1 d-block">{{ auth()->user()->name }}</p>
            {{-- <p class="text-muted left-user-info">{{ auth()->user()->role->name }}</p> --}}

            {{-- <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="">
                        <i class="mdi mdi-power"></i>
                    </a>
                </li>
            </ul> --}}
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Main</li>

                <li>
                    <a href="{{ route('dashboard.index') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span class="badge bg-success rounded-pill float-end">9+</span>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title mt-2">Operations</li>

                <li>
                    <a href="{{ route('finishing.index') }}">
                        <i class="mdi mdi-package-variant-closed"></i>
                        <span> Finishing </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('washes.index') }}">
                        <i class="mdi mdi-washing-machine"></i>
                        <span> Washes </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('productions.index') }}">
                        <i class="mdi mdi-chart-line"></i>
                        <span> Productions </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('prints.index') }}">
                        <i class="mdi mdi-draw"></i>
                        <span> Prints </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('embroideries.index') }}">
                        <i class="fi fi-tr-sewing-machine-alt"></i>
                        <span> Embroideries </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('cuttings.index') }}">
                        <i class="mdi mdi-content-cut"></i>
                        <span> Cuttings </span>
                    </a>
                </li>

                <li class="menu-title mt-2">Management</li>

                <li>
                    <a href="{{ route('orders.index') }}">
                        <i class="mdi mdi-package-variant"></i>
                        <span> Orders </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('garment_types.index') }}">
                        <i class="mdi mdi-tshirt-crew-outline"></i>
                        <span> Garments </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('factories.index') }}">
                        <i class="fa-regular fa-industry-windows"></i>
                        <span> Factories </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('lines.index') }}">
                        <i class="fa-regular fa-conveyor-belt-boxes"></i>
                        <span> Lines </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}">
                        <i class="mdi mdi-account-multiple-plus-outline"></i>
                        <span> Users </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('roles.index') }}">
                        <i class="mdi mdi-family-tree"></i>
                        <span> Roles </span>
                    </a>
                </li>

                {{-- <li>
                    <a href="#sidebarAuth" data-bs-toggle="collapse">
                        <i class="mdi mdi-account-multiple-plus-outline"></i>
                        <span> Auth Pages </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAuth">
                        <ul class="nav-second-level">
                            <li>
                                <a href="auth-login.html">Log In</a>
                            </li>
                            <li>
                                <a href="auth-register.html">Register</a>
                            </li>
                            <li>
                                <a href="auth-recoverpw.html">Recover Password</a>
                            </li>
                            <li>
                                <a href="auth-lock-screen.html">Lock Screen</a>
                            </li>
                            <li>
                                <a href="auth-confirm-mail.html">Confirm Mail</a>
                            </li>
                            <li>
                                <a href="auth-logout.html">Logout</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarExpages" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> Extra Pages </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarExpages">
                        <ul class="nav-second-level">
                            <li>
                                <a href="pages-starter.html">Starter</a>
                            </li>
                            <li>
                                <a href="pages-pricing.html">Pricing</a>
                            </li>
                            <li>
                                <a href="pages-timeline.html">Timeline</a>
                            </li>
                            <li>
                                <a href="pages-invoice.html">Invoice</a>
                            </li>
                            <li>
                                <a href="pages-faqs.html">FAQs</a>
                            </li>
                            <li>
                                <a href="pages-gallery.html">Gallery</a>
                            </li>
                            <li>
                                <a href="pages-404.html">Error 404</a>
                            </li>
                            <li>
                                <a href="pages-500.html">Error 500</a>
                            </li>
                            <li>
                                <a href="pages-maintenance.html">Maintenance</a>
                            </li>
                            <li>
                                <a href="pages-coming-soon.html">Coming Soon</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarLayouts" data-bs-toggle="collapse">
                        <i class="mdi mdi-dock-window"></i>
                        <span> Layouts </span>
                        <span class="menu-arrow"></span>

                    </a>
                    <div class="collapse" id="sidebarLayouts">
                        <ul class="nav-second-level">
                            <li>
                                <a href="layouts-horizontal.html">Horizontal</a>
                            </li>
                            <li>
                                <a href="layouts-preloader.html">Preloader</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
