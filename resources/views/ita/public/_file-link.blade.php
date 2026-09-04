{{-- One published file, shown with its type so a reader knows what opens. --}}
<a href="{{ $document->file_url }}" target="_blank" rel="noopener"
   class="group inline-flex items-start gap-2 text-sm leading-relaxed text-brand-700 hover:underline">
    <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400 group-hover:text-brand-600" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
    </svg>

    <span>
        {{ $document->title ?: $document->file_original_name }}

        @if ($document->file_extension)
            <span class="ml-1 text-xs uppercase text-slate-400">{{ $document->file_extension }}</span>
        @endif
    </span>
</a>
