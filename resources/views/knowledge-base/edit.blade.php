<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-800">Edit artikel Knowledge Base</h2><a href="{{ route('kb-articles.show', $article) }}" class="text-sm text-gray-600">Kembali</a></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-4xl sm:px-6 lg:px-8"><form method="POST" action="{{ route('kb-articles.update', $article) }}" class="rounded-lg bg-white p-6 shadow-sm">@csrf @method('PUT') @include('knowledge-base._form')</form></div></div>
</x-app-layout>
