@props(['colspan' => 1, 'icon' => 'inbox', 'title' => 'ไม่พบข้อมูล', 'description' => null])

<tr>
    <td colspan="{{ $colspan }}" class="p-0">
        <x-empty-state :icon="$icon" :title="$title" :description="$description">{{ $slot }}</x-empty-state>
    </td>
</tr>
