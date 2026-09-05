<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('patch')
            <textarea
                name="message"
                rows="5"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                >{{ old('message', $task->message) }}</textarea>

            <select
                name="status"
                class="mt-2 text-capitalize">
            @foreach ($task::STATUS as $index => $status)
              <option
                  value="{{ $index }}"
                  @if ($task->status == $index)
                  selected
                  @endif
                  >{{ __($status) }}</option>
            @endforeach
            </select>

            <x-input-error :messages="$errors->get('message')" class="mt-2" />

            <div class="mt-4 space-x-2">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <a href="{{ route('tasks.index') }}" class="text-white">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
