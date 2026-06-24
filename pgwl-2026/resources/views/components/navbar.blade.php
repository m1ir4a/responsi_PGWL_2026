<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">

        {{-- BRAND --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fa-solid fa-globe"></i> {{ $title }}
        </a>

        {{-- TOGGLER --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- MENU --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('peta') }}">
                        <i class="fa-solid fa-map"></i> Peta
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tabel') }}">
                        <i class="fa-solid fa-table"></i> Tabel
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa-solid fa-circle-info"></i> Tentang
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>