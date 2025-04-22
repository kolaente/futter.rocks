<div class="pt-20 border-t border-gray-200">
    <h2 class="text-center text-3xl font-bold font-display mb-12 text-gray-900">
        {{ __('Frequently Asked Questions (FAQ)') }}
    </h2>
    <div class="max-w-3xl mx-auto space-y-6 text-gray-900">
        <x-faq-item :question="__('Who is Futter.rocks for?')">
            {{ __('Futter.rocks is for groups who want to plan their meals and shopping lists for their camp or retreat.') }}
        </x-faq-item>
        <x-faq-item :question="__('Can I plan a national camp with this?')">
            {{ __('Not really, it is designed for one cooking group. If your event has more than that, you should look elsewhere, for example Ferkel from Kohldampf.') }}
        </x-faq-item>
        <x-faq-item :question="__('What happens to my data?')">
            {{ __('The only personal data we store is your name and email address. Because we only work with participant groups, we do not see the names or other information of the participants.') }}
            {!! __('Check out the :privacy for more details.', ['privacy' => '<a class="text-primary underline" href="'.route('policy.show').'">'.__('Privacy Policy').'</a>']) !!}
        </x-faq-item>
        <x-faq-item :question="__('Does this cost anything?')">
            {{ __('It is free to use, since I\'ve built it for my own use and wanted to share it with others. If it is useful for you, please consider donating to my coffee fund or buy me a beer should we meet at an event.') }}
        </x-faq-item>
        <x-faq-item :question="__('How can I collaborate with people from my group?')">
            {{ __('You can create a team and invite people to it. Once they are part of the team, you can work together on meal plans and recipes. All events and recipes are associated with a team.') }}
        </x-faq-item>
        <x-faq-item :question="__('I have a question or a problem, where can I get help?')">
            {!! __('You can reach me via email at :email or open an issue on :github.', ['email' => '<a class="text-primary underline" href="mailto:hallo@futter.rocks">hallo@futter.rocks</a>', 'github' => '<a class="text-primary underline" target="_blank" rel="noopener noreferrer" href="https://github.com/kolaente/futter.rocks/issues">GitHub</a>']) !!}
        </x-faq-item>
    </div>
</div>
