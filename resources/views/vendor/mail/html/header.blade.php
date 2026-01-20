@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('storage/Bossa-Nova-Health-5.svg') }}" class="logo" alt="Bossa Nova Health Logo">
@else
{!! $slot
@endif
</a>
</td>
</tr>
