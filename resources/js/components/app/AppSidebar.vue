<template>
  <b-navbar
    toggleable="lg"
    tag="aside"
    class="navbar-vertical"
    dark
  >
    <b-navbar-toggle
      target="#navbar-menu"
      data-bs-toggle="collapse"
      data-bs-target="#sidebar-menu"
    >
      <span class="navbar-toggler-icon" />
    </b-navbar-toggle>
    <router-link
      :to="{ name: 'home' }"
      class="navbar-brand navbar-brand-autodark"
    >
      <img
        src="/img/logo-white.svg"
        height="26"
        :alt="$appConfig.name"
      />
    </router-link>
    <b-navbar-nav class="flex-row d-lg-none">
      <div class="nav-item d-none d-lg-flex me-3">
        <div class="btn-list">
          <a
            href="https://github.com/tabler/tabler"
            class="btn"
            target="_blank"
            rel="noreferrer"
          >
            <brand-github-icon />
            Source code
          </a>
          <a
            href="https://github.com/sponsors/codecalm"
            class="btn"
            target="_blank"
            rel="noreferrer"
          >
            <heart-icon />
            Sponsor
          </a>
        </div>
      </div>
      <div class="d-none d-lg-flex">
        <a
          href="?theme=dark"
          class="nav-link px-0 hide-theme-dark"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          aria-label="Enable dark mode"
          data-bs-original-title="Enable dark mode"
        >
          <moon-icon />
        </a>
        <a
          href="?theme=light"
          class="nav-link px-0 hide-theme-light"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          aria-label="Enable light mode"
          data-bs-original-title="Enable light mode"
        >
          <sun-icon />
        </a>
        <div class="nav-item dropdown d-none d-md-flex me-3">
          <a
            href="#"
            class="nav-link px-0"
            data-bs-toggle="dropdown"
            tabindex="-1"
            aria-label="Show notifications"
          >
            <bell-icon />
            <span class="badge bg-red" />
          </a>
          <div
            class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"
          >
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Last updates</h3>
              </div>
              <div class="list-group list-group-flush list-group-hoverable">
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span
                        class="status-dot status-dot-animated bg-red d-block"
                      />
                    </div>
                    <div class="col text-truncate">
                      <a
                        href="#"
                        class="text-body d-block"
                        >Example 1</a
                      >
                      <div class="d-block text-muted text-truncate mt-n1">
                        Change deprecated html tags to text decoration classes
                        (#29604)
                      </div>
                    </div>
                    <div class="col-auto">
                      <a
                        href="#"
                        class="list-group-item-actions"
                      >
                        <star-icon />
                      </a>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="status-dot d-block" />
                    </div>
                    <div class="col text-truncate">
                      <a
                        href="#"
                        class="text-body d-block"
                        >Example 2</a
                      >
                      <div class="d-block text-muted text-truncate mt-n1">
                        justify-content:between ⇒ justify-content:space-between
                        (#29734)
                      </div>
                    </div>
                    <div class="col-auto">
                      <a
                        href="#"
                        class="list-group-item-actions show"
                      >
                        <star-icon />
                      </a>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="status-dot d-block" />
                    </div>
                    <div class="col text-truncate">
                      <a
                        href="#"
                        class="text-body d-block"
                        >Example 3</a
                      >
                      <div class="d-block text-muted text-truncate mt-n1">
                        Update change-version.js (#29736)
                      </div>
                    </div>
                    <div class="col-auto">
                      <a
                        href="#"
                        class="list-group-item-actions"
                      >
                        <star-icon />
                      </a>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span
                        class="status-dot status-dot-animated bg-green d-block"
                      />
                    </div>
                    <div class="col text-truncate">
                      <a
                        href="#"
                        class="text-body d-block"
                        >Example 4</a
                      >
                      <div class="d-block text-muted text-truncate mt-n1">
                        Regenerate package-lock.json (#29730)
                      </div>
                    </div>
                    <div class="col-auto">
                      <a
                        href="#"
                        class="list-group-item-actions"
                      >
                        <star-icon />
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <app-user-menu />
    </b-navbar-nav>
    <div
      id="sidebar-menu"
      class="collapse navbar-collapse"
    >
      <ul class="navbar-nav pt-lg-3">
        <li class="nav-item">
          <a
            class="nav-link"
            href="./index.html"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <home-icon />
            </span>
            <span class="nav-link-title"> Home </span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#navbar-base"
            data-bs-toggle="dropdown"
            data-bs-auto-close="false"
            role="button"
            aria-expanded="false"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <package-icon />
            </span>
            <span class="nav-link-title"> Interface </span>
          </a>
          <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
                <a
                  class="dropdown-item"
                  href="./empty.html"
                >
                  Empty page
                </a>
                <a
                  class="dropdown-item"
                  href="./accordion.html"
                >
                  Accordion
                </a>
                <a
                  class="dropdown-item"
                  href="./blank.html"
                >
                  Blank page
                </a>
                <a
                  class="dropdown-item"
                  href="./buttons.html"
                >
                  Buttons
                </a>
                <div class="dropend">
                  <a
                    class="dropdown-item dropdown-toggle"
                    href="#sidebar-cards"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="false"
                    role="button"
                    aria-expanded="false"
                  >
                    Cards
                    <span class="badge badge-sm bg-green text-uppercase ms-2"
                      >New</span
                    >
                  </a>
                  <div class="dropdown-menu">
                    <a
                      href="./cards.html"
                      class="dropdown-item"
                    >
                      Sample cards
                    </a>
                    <a
                      href="./card-actions.html"
                      class="dropdown-item"
                    >
                      Card actions
                      <span class="badge badge-sm bg-green text-uppercase ms-2"
                        >New</span
                      >
                    </a>
                    <a
                      href="./cards-masonry.html"
                      class="dropdown-item"
                    >
                      Cards Masonry
                    </a>
                  </div>
                </div>
                <a
                  class="dropdown-item"
                  href="./colors.html"
                >
                  Colors
                </a>
                <a
                  class="dropdown-item"
                  href="./datagrid.html"
                >
                  Data grid
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./datatables.html"
                >
                  Datatables
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./dropdowns.html"
                >
                  Dropdowns
                </a>
                <a
                  class="dropdown-item"
                  href="./modals.html"
                >
                  Modals
                </a>
                <a
                  class="dropdown-item"
                  href="./maps.html"
                >
                  Maps
                </a>
                <a
                  class="dropdown-item"
                  href="./map-fullsize.html"
                >
                  Map fullsize
                </a>
                <a
                  class="dropdown-item"
                  href="./maps-vector.html"
                >
                  Vector maps
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./navigation.html"
                >
                  Navigation
                </a>
                <a
                  class="dropdown-item"
                  href="./charts.html"
                >
                  Charts
                </a>
              </div>
              <div class="dropdown-menu-column">
                <a
                  class="dropdown-item"
                  href="./pagination.html"
                >
                  Pagination
                </a>
                <a
                  class="dropdown-item"
                  href="./placeholder.html"
                >
                  Placeholder
                </a>
                <a
                  class="dropdown-item"
                  href="./tabs.html"
                >
                  Tabs
                </a>
                <a
                  class="dropdown-item"
                  href="./tables.html"
                >
                  Tables
                </a>
                <a
                  class="dropdown-item"
                  href="./carousel.html"
                >
                  Carousel
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./lists.html"
                >
                  Lists
                </a>
                <a
                  class="dropdown-item"
                  href="./typography.html"
                >
                  Typography
                </a>
                <a
                  class="dropdown-item"
                  href="./offcanvas.html"
                >
                  Offcanvas
                </a>
                <a
                  class="dropdown-item"
                  href="./markdown.html"
                >
                  Markdown
                </a>
                <a
                  class="dropdown-item"
                  href="./dropzone.html"
                >
                  Dropzone
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./lightbox.html"
                >
                  Lightbox
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./tinymce.html"
                >
                  TinyMCE
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./inline-player.html"
                >
                  Inline player
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <div class="dropend">
                  <a
                    class="dropdown-item dropdown-toggle"
                    href="#sidebar-authentication"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="false"
                    role="button"
                    aria-expanded="false"
                  >
                    Authentication
                  </a>
                  <div class="dropdown-menu">
                    <a
                      href="./sign-in.html"
                      class="dropdown-item"
                    >
                      Sign in
                    </a>
                    <a
                      href="./sign-in-illustration.html"
                      class="dropdown-item"
                    >
                      Sign in with illustration
                    </a>
                    <a
                      href="./sign-in-cover.html"
                      class="dropdown-item"
                    >
                      Sign in with cover
                    </a>
                    <a
                      href="./sign-up.html"
                      class="dropdown-item"
                    >
                      Sign up
                    </a>
                    <a
                      href="./forgot-password.html"
                      class="dropdown-item"
                    >
                      Forgot password
                    </a>
                    <a
                      href="./terms-of-service.html"
                      class="dropdown-item"
                    >
                      Terms of service
                    </a>
                    <a
                      href="./auth-lock.html"
                      class="dropdown-item"
                    >
                      Lock screen
                    </a>
                  </div>
                </div>
                <div class="dropend">
                  <a
                    class="dropdown-item dropdown-toggle"
                    href="#sidebar-error"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="false"
                    role="button"
                    aria-expanded="false"
                  >
                    <file-minus-icon />
                    Error pages
                  </a>
                  <div class="dropdown-menu">
                    <a
                      href="./error-404.html"
                      class="dropdown-item"
                    >
                      404 page
                    </a>
                    <a
                      href="./error-500.html"
                      class="dropdown-item"
                    >
                      500 page
                    </a>
                    <a
                      href="./error-maintenance.html"
                      class="dropdown-item"
                    >
                      Maintenance page
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            href="./form-elements.html"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <checkbox-icon />
            </span>
            <span class="nav-link-title"> Forms </span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#navbar-extra"
            data-bs-toggle="dropdown"
            data-bs-auto-close="false"
            role="button"
            aria-expanded="false"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <star-icon />
            </span>
            <span class="nav-link-title"> Extra </span>
          </a>
          <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
                <a
                  class="dropdown-item"
                  href="./activity.html"
                >
                  Activity
                </a>
                <a
                  class="dropdown-item"
                  href="./gallery.html"
                >
                  Gallery
                </a>
                <a
                  class="dropdown-item"
                  href="./invoice.html"
                >
                  Invoice
                </a>
                <a
                  class="dropdown-item"
                  href="./search-results.html"
                >
                  Search results
                </a>
                <a
                  class="dropdown-item"
                  href="./pricing.html"
                >
                  Pricing cards
                </a>
                <a
                  class="dropdown-item"
                  href="./pricing-table.html"
                >
                  Pricing table
                </a>
                <a
                  class="dropdown-item"
                  href="./faq.html"
                >
                  FAQ
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./users.html"
                >
                  Users
                </a>
                <a
                  class="dropdown-item"
                  href="./license.html"
                >
                  License
                </a>
              </div>
              <div class="dropdown-menu-column">
                <a
                  class="dropdown-item"
                  href="./logs.html"
                >
                  Logs
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./music.html"
                >
                  Music
                </a>
                <a
                  class="dropdown-item"
                  href="./tasks.html"
                >
                  Tasks
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./uptime.html"
                >
                  Uptime monitor
                </a>
                <a
                  class="dropdown-item"
                  href="./widgets.html"
                >
                  Widgets
                </a>
                <a
                  class="dropdown-item"
                  href="./wizard.html"
                >
                  Wizard
                </a>
                <a
                  class="dropdown-item"
                  href="./settings.html"
                >
                  Settings
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./job-listing.html"
                >
                  Job listing
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item active dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#navbar-layout"
            data-bs-toggle="dropdown"
            data-bs-auto-close="false"
            role="button"
            aria-expanded="true"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <layout-2-icon />
            </span>
            <span class="nav-link-title"> Layout </span>
          </a>
          <div class="dropdown-menu show">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
                <a
                  class="dropdown-item"
                  href="./layout-horizontal.html"
                >
                  Horizontal
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-boxed.html"
                >
                  Boxed
                  <span class="badge badge-sm bg-green text-uppercase ms-2"
                    >New</span
                  >
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-vertical.html"
                >
                  Vertical
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-vertical-transparent.html"
                >
                  Vertical transparent
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-vertical-right.html"
                >
                  Right vertical
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-condensed.html"
                >
                  Condensed
                </a>
                <a
                  class="dropdown-item active"
                  href="./layout-combo.html"
                >
                  Combined
                </a>
              </div>
              <div class="dropdown-menu-column">
                <a
                  class="dropdown-item"
                  href="./layout-navbar-dark.html"
                >
                  Navbar dark
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-navbar-sticky.html"
                >
                  Navbar sticky
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-navbar-overlap.html"
                >
                  Navbar overlap
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-rtl.html"
                >
                  RTL mode
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-fluid.html"
                >
                  Fluid
                </a>
                <a
                  class="dropdown-item"
                  href="./layout-fluid-vertical.html"
                >
                  Fluid vertical
                </a>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            href="./icons.html"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <ghost-icon />
            </span>
            <span class="nav-link-title"> 2853 icons </span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#navbar-help"
            data-bs-toggle="dropdown"
            data-bs-auto-close="false"
            role="button"
            aria-expanded="false"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <lifebuoy-icon />
            </span>
            <span class="nav-link-title"> Help </span>
          </a>
          <div class="dropdown-menu">
            <a
              class="dropdown-item"
              href="./docs/index.html"
            >
              Documentation
            </a>
            <a
              class="dropdown-item"
              href="./changelog.html"
            >
              Changelog
            </a>
            <a
              class="dropdown-item"
              href="https://github.com/tabler/tabler"
              target="_blank"
              rel="noopener"
            >
              Source code
            </a>
            <a
              class="dropdown-item text-pink"
              href="https://github.com/sponsors/codecalm"
              target="_blank"
              rel="noopener"
            >
              <heart-icon />
              Sponsor project!
            </a>
          </div>
        </li>
      </ul>
    </div>
  </b-navbar>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
</script>
