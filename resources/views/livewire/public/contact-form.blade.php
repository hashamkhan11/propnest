<div>
    <form wire:submit="send" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="contact-name" value="Name" />
                <x-input wire:model="name" id="contact-name" type="text" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact-email" value="Email" />
                <x-input wire:model="email" id="contact-email" type="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="contact-subject" value="Subject" />
            <x-input wire:model="subject" id="contact-subject" type="text" />
            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="contact-message" value="Message" />
            <textarea wire:model="message" id="contact-message" rows="5" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full transition-colors duration-150"></textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-button type="submit">Send Message</x-button>

            @if (session('success'))
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            @endif
        </div>
    </form>
</div>
