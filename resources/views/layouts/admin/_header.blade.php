<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box horizontal-logo">
                    <a href="" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo).'.'.brand()->logo_ext) }}" alt="" width="40" height="">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo).'.'.brand()->logo_ext) }}" alt="" width="40" height="">
                        </span>
                    </a>
                    <a href="" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo_light).'.'.brand()->logo_light_ext) }}" alt="" width="40" height="">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo_light).'.'.brand()->logo_light_ext) }}" alt="" width="40" height="">
                        </span>
                    </a>
                </div>
                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <div id="live-clock" class="text-white fs-15 mt-4 fw-semibold"></div>
            </div>
            <div class="d-flex align-items-center">
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            @if (auth()->user()->photo)
                                <img class="rounded-circle header-profile-user" src="" alt="Header Avatar">
                            @else
                                <img class="rounded-circle p-1 bg-secondary header-profile-user" src="{{ asset('storage/images/accounts/avatar.png') }}" alt="Header Avatar">
                            @endif
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text">{{ auth()->user()->name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-13 text-muted user-name-sub-text">{{ auth()->user()->email }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome, {{ auth()->user()->name }}</h6>
                        <a class="dropdown-item" href="#">
                            <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> 
                            <span class="align-middle">Profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> 
                            <span class="align-middle">Change Password</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('logout.perform') }}">
                            <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> 
                            <span class="align-middle" data-key="t-logout">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>