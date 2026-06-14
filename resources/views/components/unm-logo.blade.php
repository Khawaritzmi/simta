<img {{ $attributes->merge([
    'src' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/Logo_Universitas_Negeri_Makassar.png'))),
    'alt' => 'Logo UNM',
]) }}>
