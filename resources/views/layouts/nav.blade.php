<div class="horizontal-menu">
    <nav class="navbar top-navbar col-lg-12 col-12 p-0">
        <div class="container">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo" href="/"><img src="/images/logo.png" alt="logo"/></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end pr-0">
            <ul class="navbar-nav d-none d-lg-flex align-items-center mr-lg-2">
            <li class="nav-item logo-font">
                兆基內部資訊系統
            </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown  d-flex" style="top: -8px">
                <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown">
                <i class="typcn typcn-bell mr-0"></i>
                <span class="count bg-danger">2</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                <p class="mb-0 font-weight-normal float-left dropdown-header">站內通知</p>
                <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                        <i class="typcn typcn-info-large mx-0"></i>
                    </div>
                    </div>
                    <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">Application Error</h6>
                    <p class="font-weight-light small-text mb-0">
                        Just now
                    </p>
                    </div>
                </a>
                <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                    <div class="preview-icon bg-warning">
                        <i class="typcn typcn-cog mx-0"></i>
                    </div>
                    </div>
                    <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">Settings</h6>
                    <p class="font-weight-light small-text mb-0">
                        Private message
                    </p>
                    </div>
                </a>
                <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                    <div class="preview-icon bg-info">
                        <i class="typcn typcn-user-outline mx-0"></i>
                    </div>
                    </div>
                    <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">New user registration</h6>
                    <p class="font-weight-light small-text mb-0">
                        2 days ago
                    </p>
                    </div>
                </a>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle  pl-0 pr-0" href="#" data-toggle="dropdown" id="profileDropdown">
                <i class="typcn typcn-user-outline mr-0"></i>
                <span class="nav-profile-name">{{{ Auth::user()->name }}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item">
                <i class="typcn typcn-cog text-primary"></i>
                設定
                </a>
                <a class="dropdown-item" href="/logout">
                <i class="typcn typcn-power text-primary"></i>
                登出
                </a>
                </div>
            </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
            <span class="typcn typcn-th-menu"></span>
            </button>
        </div>
        </div>
    </nav>
    <nav class="bottom-navbar">
        <div class="container">
        <ul class="nav page-navigation">
            <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="typcn typcn-device-desktop menu-icon"></i>
                <span class="menu-title">首頁</span>
            </a>
            </li>
            <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="typcn typcn-briefcase menu-icon"></i>
                <span class="menu-title">系統管理</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul class="submenu-item">
                <li class="nav-item"><a class="nav-link" href="/users">使用者管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/audits">資料稽核管理</a></li>
                </ul>
            </div>
            </li>
            <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="typcn typcn-film menu-icon"></i>
                <span class="menu-title">管理處</span>
                <i class="menu-arrow"></i></a>
            <div class="submenu">
                <ul class="submenu-item">
                <li class="nav-item"><a class="nav-link" href="/landlords">房東管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/landlordContracts">房東合約管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/tenants">租客管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/tenantContracts">租客合約管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/buildings">物件管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/keys">鑰匙管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/debtCollections">催收管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/maintenances">清潔維修管理</a></li>
                <li class="nav-item"><a class="nav-link" href="/shareholders">股東管理</a></li>
                </ul>
            </div>
            </li>
            <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="typcn typcn-th-small-outline menu-icon"></i>
                <span class="menu-title">帳務處</span>
                <i class="menu-arrow"></i></a>
                <div class="submenu">
                    <ul class="submenu-item">
                    <li class="nav-item"><a class="nav-link" href="/deposits">訂金管理</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tenantElectricityPayments">電費管理</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tenantPayments">租客應繳費用管理</a></li>
                    <li class="nav-item"><a class="nav-link" href="/landlordPayments">房東應繳費用管理</a></li>
                    <li class="nav-item"><a class="nav-link" href="/receipts">發票單據管理</a></li>
                    <li class="nav-item"><a class="nav-link" href="/monthlyReports">月結單管理</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/forms/code_editor.html">Code Editor</a></li>
                    </ul>
                </div>
            </li>
        </ul>
        </div>
    </nav>
</div>