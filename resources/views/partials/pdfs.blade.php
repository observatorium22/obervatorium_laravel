@if ($pdfs->isNotEmpty())
    <table class="pdf_list">
        @foreach ($pdfs as $pdf)
            <tr>
                <td>
                    <a href="{{ $pdf->url() }}" target="_blank" rel="noopener">
                        <img class="pdf_icon" src="{{ asset('graphics/icon_pdf_color.gif') }}" alt="">
                    </a>
                </td>
                <td>
                    <a class="list_item_compact_a" href="{{ $pdf->url() }}" target="_blank" rel="noopener">{{ $pdf->title }}</a>
                </td>
            </tr>
        @endforeach
    </table>
@endif
