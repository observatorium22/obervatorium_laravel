<div id="observatorium_list">
    @foreach ($items as $item)
        <a class="{{ $selected && $selected->is($item) ? 'obs_list_item_title_a_selected' : 'obs_list_item_title_a' }}"
           href="{{ route('about.show', $item) }}">{{ $item->title }}</a><br>
    @endforeach
    <p><a class="obs_list_item_title_a" href="{{ route('newsletter') }}" target="_blank" rel="noopener">Subscribe to our newsletter</a></p>
</div>
