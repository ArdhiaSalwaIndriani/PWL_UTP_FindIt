@if (session('sukses'))
    <div class="mb-4 border border-sea bg-sea/20 px-4 py-3 text-sm text-amalfi" role="status">{{ session('sukses') }}</div>
@endif
@if (session('galat'))
    <div class="mb-4 border border-bahaya/30 bg-bahaya-muda px-4 py-3 text-sm text-bahaya" role="alert">{{ session('galat') }}</div>
@endif
