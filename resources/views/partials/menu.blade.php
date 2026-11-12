<header id="desktop_menu_hamburger">
    <button type="button" id="menu_toggle" aria-label="Menu">
        <img class="menu_icon_img" src="{{ asset('graphics/menu_icon3.png') }}" alt="">
    </button>
    <a class="{{ $section === 'gallery' ? 'nav_a_selected' : 'nav_a' }}" href="{{ route('gallery') }}">OBSERVATORIUM</a>
    @if ($section === 'projects')
        <a class="nav_a_selected" href="{{ route('projects') }}">PROJECTS</a>
    @elseif ($section === 'news')
        <a class="nav_a_selected" href="{{ route('news') }}">NEWS</a>
    @elseif ($section === 'blog')
        <a class="nav_a_selected" href="{{ route('blog') }}">BLOG</a>
    @elseif ($section === 'about')
        <a class="nav_a_selected" href="{{ route('about') }}">ABOUT</a>
    @endif
    @if ($section === 'gallery' && filled($settings->mission_statement))
        <div id="mission_statement">{{ $settings->mission_statement }}</div>
    @endif
</header>

<nav class="main_nav_hamburger" hidden>
    <a class="nav_a" href="{{ route('news') }}">NEWS</a>
    <a class="nav_a" href="{{ route('projects') }}">PROJECTS</a>
    <a class="nav_a" href="{{ route('blog') }}">BLOG</a>
    <a class="nav_a" href="{{ route('about') }}">ABOUT</a>
</nav>
