<x-mail::message>

    <h1>
        Schedule Time Changed Notification
    </h1>

    <p>Hello,</p>
    <p>This is to inform you that the schedule time has been changed.</p>
    <x-mail::panel>
        <p><strong>Old Start Time:</strong> {{ $oldStart }}</p>
        <p><strong>New Start Time:</strong> {{ $newStart }}</p>
        @if (!empty($changeReason))
            <p><strong>Reason for Change:</strong> {{ $changeReason }}</p>
        @endif
        <p>If you have any questions or concerns, please don't hesitate to reach out to us at
            {{ config('mail.from.address') }}.</p>
        <p>Regards,<br>{{ config('app.name') }} Team</p>
    </x-mail::panel>

</x-mail::message>
