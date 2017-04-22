<?php
/**
 * @package	Horme 3 Pro template
 * @author Spyros Petrakis
 * @copyright	Copyright (C) 2015 Olympiansoft PC IKE. All rights reserved.
 * @license		GNU General Public License version 2 or later
 *
 */

defined('_JEXEC') or die;
// Background image
if ($background) {
  $doc->addStyleDeclaration("
    body{
      background: url('" . JURI::root() . $background . "') no-repeat fixed center center;
      background-size: cover;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
    }
  ");
}

// Body Background color
if ($this->params->get('bgcolor')) {
    $doc->addStyleDeclaration("
      body{
        background-color:" . $this->params->get('bgcolor') . ";
      }
    ");
}

// Frame layout Background color
if ($this->params->get('framecolor')) {
    $doc->addStyleDeclaration("
      #frame.frame{
        background-color:" . $this->params->get('framecolor') . ";
      }
    ");
}

// Content Background color
if ($this->params->get('boxcolor')) {
    $doc->addStyleDeclaration("
      #fds-main > .container{
        background-color:" . $this->params->get('boxcolor') . ";
        padding-top: 15px;
        max-width: 1140px;
      }
    ");
}

// Fonts
if ($this->params->get('fontcolor')) {
  $doc->addStyleDeclaration("
    body {
      color:" . $this->params->get('fontcolor') . ";
    }
  ");
}
if ($this->params->get('fontsize')) {
  $doc->addStyleDeclaration("
    html, body {
      font-size:" . $this->params->get('fontsize') . ";
    }
  ");
}

// Links
if ($this->params->get('linkcolor')) {
  $doc->addStyleDeclaration("
    a, .btn-link {
      color:" . $this->params->get('linkcolor') . ";
    }

    .nav-pills > li.active > a,
    .nav-pills > li.active > a:hover,
    .nav-pills > li.active > a:focus {
      background-color:" . $this->params->get('linkcolor') . ";
    }

  ");
}
if ($this->params->get('linkhvcolor')) {
  $doc->addStyleDeclaration("
    a:hover,
    a:focus,
    .pagination > li > a:hover,
    .pagination > li > span:hover,
    .pagination > li > a:focus,
    .pagination > li > span:focus,
    .btn-link:hover, .btn-link:focus {
      color:" . $this->params->get('linkhvcolor') . ";
    }
  ");
}

// Misc
if ($this->params->get('bordercolor')) {
  $doc->addStyleDeclaration("
    hr,
    .box .border,
    .border > h3,
    .form-control,
    .thumbnail,
    .page-header {
      border-color:" . $this->params->get('bordercolor') . ";
    }
  ");
}

// Toolbar
if ($this->params->get('vmtoolbgcolor')) {
  $doc->addStyleDeclaration("
    #fds-vmtoolbar {
      background-color:" . $this->params->get('vmtoolbgcolor') . ";
    }
  ");
}
if ($this->params->get('toolbgcolor')) {
  $doc->addStyleDeclaration("
    #fds-toolbar {
      background-color:" . $this->params->get('toolbgcolor') . ";
      border-bottom-color: " . $this->params->get('toolbgcolor') . ";
    }
  ");
}
if ($this->params->get('toolfontcolor')) {
  $doc->addStyleDeclaration("
    .toolbar {
      color:" . $this->params->get('toolfontcolor') . " !important;
    }
  ");
}

// Menu
if ($this->params->get('menubgcolor')) {
  $doc->addStyleDeclaration("
    .navbar-default,
    .navbar-default .dropdown-menu,
    .flyout-menu,
    .navbar-inverse .dropdown-menu,
    #offcanvas .flyout-menu,
    .off-canvas-wrapper {
      background-color:" . $this->params->get('menubgcolor') . ";
    }
  ");
}
if ($this->params->get('activebgcolor')) {
  $doc->addStyleDeclaration("
    .navbar .nav > li:hover,
    .navbar .nav > li.active > a,
    .navbar .nav > li.active > span,
    .navbar-default .navbar-nav > li > a:hover,
    .navbar-default .navbar-nav > li > a:focus,
    .navbar-default .navbar-nav > .active > a,
    .navbar-default .navbar-nav > .active > a:hover,
    .navbar-default .navbar-nav > .active > a:focus,
    .navbar-default .dropdown-menu > li > a:hover,
    .navbar-default .dropdown-menu > .active > a,
    .navbar-default .dropdown-menu > .active > a:hover,
    .navbar-default .dropdown-menu > li > span:hover,
    .navbar-default .dropdown-menu > .active > span,
    .navbar-default .dropdown-menu > .active > span:hover,
    .flyout-menu > li > span:hover,
    .flyout-menu > .active > span,
    .flyout-menu > .active > span:hover,
    .flyout-menu > li > a:hover,
    .flyout-menu > li > a:focus,
    .flyout-menu > li > span:hover,
    .flyout-menu > li > span:focus,
    .navbar-inverse .navbar-nav > .active > a,
    .navbar-inverse .navbar-nav > .active > a:hover,
    .navbar-inverse .navbar-nav > .active > a:focus,
    .navbar-inverse .navbar-nav > li > a:hover,
    .navbar-inverse .navbar-nav > li > a:focus,
    #offcanvas .navbar-nav > li > span:hover,
    #offcanvas .dropdown-menu span:hover,
    .navbar-inverse .dropdown-menu > li > a:hover,
    .navbar-inverse .dropdown-menu > .active > a,
    .navbar-inverse .dropdown-menu > .active > a:hover,
    .mega + ul .flyout-menu > li > span:hover,
    .mega + ul .flyout-menu > .active > span,
    .mega + ul .flyout-menu > .active > span:hover,
    .mega + ul .flyout-menu > li > a:hover,
    .mega + ul .flyout-menu > .active > a,
    .mega + ul .flyout-menu > .active > a:hover {
    	background-color:" . $this->params->get('activebgcolor') . ";
      border-color:" . $this->params->get('activebgcolor') . ";
    }
    .mega + ul > li > a, .mega + ul > li > span {
    	border-bottom: 1px solid " . $this->params->get('activebgcolor') . ";
    }
  ");
}
if ($this->params->get('menufontcolor')) {
  $doc->addStyleDeclaration("
    .navbar-default .navbar-nav a,
    .navbar-default .navbar-nav span,
    .navbar-nav .dropdown-menu > li.parent::after,
    .flyout-menu > li.parent::after,
    #offcanvas a,
    #offcanvas span,
    #offcanvas-toggle{
      color:" . $this->params->get('menufontcolor') . " !important;
    }
  ");
}

// Bottom-c position
if ($this->params->get('bottombgcolor')) {
  $doc->addStyleDeclaration("
    #fds-bottom-c {
      background-color:" . $this->params->get('bottombgcolor') . ";
      border-top-color:" . $this->params->get('bottombgcolor') . ";
    }
  ");
}

if ($this->params->get('bottomfontcolor')) {
  $doc->addStyleDeclaration("
    #fds-bottom-c {
      color:" . $this->params->get('bottomfontcolor') . ";
    }
  ");
}

// Footer
if ($this->params->get('footerbgcolor')) {
  $doc->addStyleDeclaration("
    footer {
    	background-color:" . $this->params->get('footerbgcolor') . ";
    }
    footer .container {
      border-top: none;
    }
  ");
}
if ($this->params->get('footerfontcolor')) {
  $doc->addStyleDeclaration("
    footer {
    	color:" . $this->params->get('footerfontcolor') . ";
    }
  ");
}

// Buttons
// Default
if ($this->params->get('btn-default')) {
  $doc->addStyleDeclaration("
    .btn-default {
    	background-color:" . $this->params->get('btn-default') . ";
      border-color:" . $this->params->get('btn-default') . ";
    }
  ");
}

if ($this->params->get('btn-default-hover')) {
  $doc->addStyleDeclaration("
    .btn-default:hover {
    	background-color:" . $this->params->get('btn-default-hover') . ";
      border-color:" . $this->params->get('btn-default-hover') . ";
    }
  ");
}

if ($this->params->get('btn-default-text')) {
  $doc->addStyleDeclaration("
    .btn-default,
    .btn-default:hover {
    	color:" . $this->params->get('btn-default-text') . ";
    }
  ");
}
// Primary
if ($this->params->get('btn-primary')) {
  $doc->addStyleDeclaration("
    .btn-primary {
    	background-color:" . $this->params->get('btn-primary') . ";
      border-color:" . $this->params->get('btn-primary') . ";
    }
  ");
}

if ($this->params->get('btn-primary-hover')) {
  $doc->addStyleDeclaration("
    .btn-primary:hover {
    	background-color:" . $this->params->get('btn-primary-hover') . ";
      border-color:" . $this->params->get('btn-primary-hover') . ";
    }
  ");
}

if ($this->params->get('btn-primary-text')) {
  $doc->addStyleDeclaration("
    .btn-primary,
    .btn-primary:hover {
    	color:" . $this->params->get('btn-primary-text') . ";
    }
  ");
}
// Success
if ($this->params->get('btn-success')) {
  $doc->addStyleDeclaration("
    .btn-success {
    	background-color:" . $this->params->get('btn-success') . ";
      border-color:" . $this->params->get('btn-success') . ";
    }
  ");
}

if ($this->params->get('btn-success-hover')) {
  $doc->addStyleDeclaration("
    .btn-success:hover {
    	background-color:" . $this->params->get('btn-success-hover') . ";
      border-color:" . $this->params->get('btn-success-hover') . ";
    }
  ");
}

if ($this->params->get('btn-success-text')) {
  $doc->addStyleDeclaration("
    .btn-success,
    .btn-success:hover {
    	color:" . $this->params->get('btn-success-text') . ";
    }
  ");
}
// Warning
if ($this->params->get('btn-warning')) {
  $doc->addStyleDeclaration("
    .btn-warning {
    	background-color:" . $this->params->get('btn-warning') . ";
      border-color:" . $this->params->get('btn-warning') . ";
    }
  ");
}

if ($this->params->get('btn-warning-hover')) {
  $doc->addStyleDeclaration("
    .btn-warning:hover {
    	background-color:" . $this->params->get('btn-warning-hover') . ";
      border-color:" . $this->params->get('btn-warning-hover') . ";
    }
  ");
}

if ($this->params->get('btn-warning-text')) {
  $doc->addStyleDeclaration("
    .btn-warning,
    .btn-warning:hover {
    	color:" . $this->params->get('btn-warning-text') . ";
    }
  ");
}
// Danger
if ($this->params->get('btn-danger')) {
  $doc->addStyleDeclaration("
    .btn-danger {
    	background-color:" . $this->params->get('btn-danger') . ";
      border-color:" . $this->params->get('btn-danger') . ";
    }
  ");
}

if ($this->params->get('btn-danger-hover')) {
  $doc->addStyleDeclaration("
    .btn-danger:hover {
    	background-color:" . $this->params->get('btn-danger-hover') . ";
      border-color:" . $this->params->get('btn-danger-hover') . ";
    }
  ");
}

if ($this->params->get('btn-danger-text')) {
  $doc->addStyleDeclaration("
    .btn-danger,
    .btn-danger:hover {
    	color:" . $this->params->get('btn-danger-text') . ";
    }
  ");
}
