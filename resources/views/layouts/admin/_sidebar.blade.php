<div class="app-menu navbar-menu">
    <!-- logo -->
    <div class="navbar-brand-box">
        <!-- dark logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo).'.'.brand()->logo_ext) }}" alt="" width="80" height="">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo).'.'.brand()->logo_ext) }}" alt="" width="80" height="">
            </span>
        </a>
        <!-- light logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo_light).'.'.brand()->logo_light_ext) }}" alt="" width="80" height="">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('storage/images/brand/'.base64_decode(brand()->logo_light).'.'.brand()->logo_light_ext) }}" alt="" width="80" height="">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover"><i class="ri-record-circle-line"></i></button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Navigations</span></li>
                <?php if (menu_sidebar(auth('admin')->user()->user_group_id)) : ?>
                    <?php $menus = menu_sidebar(auth('admin')->user()->user_group_id) ?>
                    <?php foreach ($menus as $menu) : ?>
                        <?php if ($menu['nodes']) : ?>
                            <?php $show = '' ?> 
                            <?php $active = '' ?> 
                            <?php foreach ($menu['nodes'] as $row) : ?> 
                                <?php if (request()->is($row['url'])) : ?>
                                    <?php $show = 'show' ?> 
                                    <?php $active = 'active' ?> 
                                <?php endif; ?>
                                <?php if ($row['nodes']) : ?>
                                    <?php foreach ($row['nodes'] as $row2) : ?> 
                                        <?php if (request()->is($row2['url'])) : ?>
                                            <?php $show = 'show' ?> 
                                            <?php $active = 'active' ?> 
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ $active }}" href="#sidebar-{{ $menu['id'] }}" data-bs-toggle="collapse" role="button">
                                    <i class="{{ $menu['icon'] }}"></i> 
                                    <span>{{ ucwords($menu['name']) }}</span>
                                </a>
                                <div class="collapse menu-dropdown {{ $show }}" id="sidebar-{{ $menu['id'] }}">
                                    <ul class="nav nav-sm flex-column">
                                        <?php foreach ($menu['nodes'] as $child) : ?>
                                            <?php if ($child['nodes']) : ?>   
                                                <?php $show2 = '' ?> 
                                                <?php $active2 = '' ?> 
                                                <?php foreach ($child['nodes'] as $row2) : ?> 
                                                    <?php if (request()->is($row2['url'])) : ?>
                                                        <?php $show2 = 'show' ?> 
                                                        <?php $active2 = 'active' ?> 
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <li class="nav-item">
                                                    <a href="#sidebar-{{ $child['id'] }}" class="nav-link {{ $active2 }}" data-bs-toggle="collapse" role="button">{{ ucwords($child['name']) }}</a>
                                                    <div class="collapse menu-dropdown {{ $show2 }}" id="sidebar-{{ $child['id'] }}">
                                                        <ul class="nav nav-sm flex-column">
                                                            <?php foreach ($child['nodes'] as $child2) : ?>
                                                                <?php $show3 = '' ?> 
                                                                <?php $active3 = '' ?> 
                                                                <?php if (request()->is($child2['url'])) : ?>
                                                                    <?php $show3 = 'show' ?> 
                                                                    <?php $active3 = 'active' ?> 
                                                                <?php endif; ?>
                                                                <li class="nav-item">
                                                                    <a href="{{ url($child2['url']) }}" class="nav-link {{ $active3 }}">{{ ucwords($child2['name']) }}</a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </li>  
                                            <?php else : ?>
                                                <?php $show2 = '' ?> 
                                                <?php $active2 = '' ?> 
                                                <?php if (request()->is($child['url'])) : ?>
                                                    <?php $show2 = 'show' ?> 
                                                    <?php $active2 = 'active' ?> 
                                                <?php endif; ?>
                                                <li class="nav-item">
                                                    <a href="{{ url($child['url']) }}" class="nav-link {{ $active2 }}">{{ ucwords($child['name']) }}</a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ (request()->is($menu['url']) ? 'active' : '') }}" href="{{ url($menu['url']) }}">
                                    <i class="{{ $menu['icon'] }}"></i> 
                                    <span>{{ ucwords($menu['name']) }}</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <!-- sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- left sidebar end -->