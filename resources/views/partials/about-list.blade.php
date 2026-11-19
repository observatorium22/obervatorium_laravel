<div id="observatorium_list">
    @foreach ($items as $item)
        <a class="{{ $selected && $selected->is($item) ? 'obs_list_item_title_a_selected' : 'obs_list_item_title_a' }}"
           href="{{ route('about.show', $item) }}">{{ $item->title }}</a><br>
    @endforeach
    <p>
        <a class="obs_list_item_title_a" href="{{ route('newsletter') }}" target="_blank" rel="noopener">Subscribe to our newsletter</a><br>
        @if (filled($settings->instagram_url))
            <a class="obs_list_item_title_a" href="{{ $settings->instagram_url }}" target="_blank" rel="noopener">Instagram</a><br>
        @endif
        @if (filled($settings->facebook_url))
            <a class="obs_list_item_title_a" href="{{ $settings->facebook_url }}" target="_blank" rel="noopener">Facebook</a>@if (filled($settings->linkedin_url))<br>@endif
        @endif
        @if (filled($settings->linkedin_url))
            <a class="obs_list_item_title_a" href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a>
        @endif
    </p>
</div>
