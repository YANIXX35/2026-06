@php $cat = $categorie ?? null; @endphp

<div style="display:flex;flex-direction:column;gap:16px;">
    <div>
        <label style="display:block;font-size:.74rem;font-weight:800;color:var(--text);letter-spacing:.2px;margin-bottom:5px;text-transform:uppercase;">
            Nom *
        </label>
        <input type="text" name="nom" value="{{ old('nom', $cat->nom ?? '') }}"
               style="width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:.88rem;outline:none;transition:.2s;box-sizing:border-box;"
               onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'"
               placeholder="Ex: Fruits & Légumes" required>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
            <label style="display:block;font-size:.74rem;font-weight:800;color:var(--text);letter-spacing:.2px;margin-bottom:5px;text-transform:uppercase;">
                Icône (emoji) *
            </label>
            <input type="text" name="icone" id="icone-input" value="{{ old('icone', $cat->icone ?? '') }}"
                   style="width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:1.4rem;text-align:center;outline:none;transition:.2s;box-sizing:border-box;"
                   onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'"
                   placeholder="🍎" maxlength="4" required>
        </div>
        <div>
            <label style="display:block;font-size:.74rem;font-weight:800;color:var(--text);letter-spacing:.2px;margin-bottom:5px;text-transform:uppercase;">
                Couleur *
            </label>
            <div style="display:flex;align-items:center;gap:8px;">
                <input type="color" name="couleur" id="couleur-picker" value="{{ old('couleur', $cat->couleur ?? '#16a34a') }}"
                       style="width:44px;height:44px;border:1.5px solid var(--border);border-radius:10px;padding:2px;cursor:pointer;">
                <input type="text" id="couleur-text" value="{{ old('couleur', $cat->couleur ?? '#16a34a') }}"
                       style="flex:1;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:.82rem;font-family:monospace;outline:none;transition:.2s;box-sizing:border-box;"
                       placeholder="#16a34a" readonly>
            </div>
        </div>
    </div>

    <div>
        <label style="display:block;font-size:.74rem;font-weight:800;color:var(--text);letter-spacing:.2px;margin-bottom:5px;text-transform:uppercase;">
            Description
        </label>
        <input type="text" name="description" value="{{ old('description', $cat->description ?? '') }}"
               style="width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:.88rem;outline:none;transition:.2s;box-sizing:border-box;"
               onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'"
               placeholder="Description courte de la catégorie">
    </div>

    {{-- Aperçu --}}
    <div style="background:var(--surface);border-radius:10px;padding:14px;border:1px solid var(--border);">
        <div style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;margin-bottom:8px;">Aperçu</div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:#fff;border-radius:50px;padding:6px 14px;border:2px solid var(--border);">
            <span id="preview-icon" style="font-size:1.1rem;">{{ $cat->icone ?? '🏷' }}</span>
            <span id="preview-nom" style="font-weight:700;font-size:.85rem;">{{ $cat->nom ?? 'Nom catégorie' }}</span>
            <span id="preview-dot" style="width:10px;height:10px;border-radius:50%;background:{{ $cat->couleur ?? '#16a34a' }};flex-shrink:0;"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const couleurPicker = document.getElementById('couleur-picker');
    const couleurText   = document.getElementById('couleur-text');
    const previewDot    = document.getElementById('preview-dot');
    const iconeInput    = document.getElementById('icone-input');
    const previewIcon   = document.getElementById('preview-icon');
    const nomInput      = document.querySelector('input[name="nom"]');
    const previewNom    = document.getElementById('preview-nom');

    couleurPicker.addEventListener('input', () => {
        couleurText.value = couleurPicker.value;
        previewDot.style.background = couleurPicker.value;
    });
    iconeInput.addEventListener('input', () => {
        previewIcon.textContent = iconeInput.value || '🏷';
    });
    nomInput.addEventListener('input', () => {
        previewNom.textContent = nomInput.value || 'Nom catégorie';
    });
</script>
@endpush
