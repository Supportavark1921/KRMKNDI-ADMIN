<div class="store-card" style="max-width:800px">
    <div class="form-grid">

        <div class="form-group">
            <label class="form-label">Mataji *</label>
            <select name="mataji_id" class="form-input" required>
                <option value="">— Select —</option>
                @foreach($matajis as $m)
                <option value="{{ $m->id }}" @selected(old('mataji_id', $order->mataji_id ?? '') == $m->id)>{{ $m->name }} ({{ $m->temple_name }})</option>
                @endforeach
            </select>
            @error('mataji_id')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Order type *</label>
            <select name="type" class="form-input">
                @foreach($types as $t)
                <option value="{{ $t }}" @selected(old('type', $order->type ?? 'sale') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Customer name</label>
            <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name ?? '') }}" class="form-input" placeholder="Walk-in customer">
        </div>

        <div class="form-group">
            <label class="form-label">Customer phone</label>
            <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone ?? '') }}" class="form-input" placeholder="+91…">
        </div>

        <div class="form-group">
            <label class="form-label">Discount (₹)</label>
            <input type="number" name="discount" value="{{ old('discount', $order->discount ?? 0) }}" min="0" step="0.01" class="form-input" style="width:120px">
        </div>

        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="2" class="form-input">{{ old('notes', $order->notes ?? '') }}</textarea>
        </div>

    </div>

    {{-- Line items --}}
    <h3 style="margin:20px 0 10px">Products</h3>
    <div id="items-container">
        @if(isset($order) && $order->items->count())
            @foreach($order->items as $item)
            <div class="order-item-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center">
                <select name="product_ids[]" class="form-input" style="flex:1">
                    <option value="">— Product —</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected($p->id == $item->product_id)>
                        {{ $p->name }} (₹{{ $p->price }}, stock: {{ $p->inventory?->available_stock ?? 0 }})
                    </option>
                    @endforeach
                </select>
                <input type="number" name="quantities[]" value="{{ $item->quantity }}" min="1" class="form-input" style="width:80px" placeholder="Qty">
                <button type="button" class="btn-sm btn-danger remove-item">✕</button>
            </div>
            @endforeach
        @else
        <div class="order-item-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center">
            <select name="product_ids[]" class="form-input" style="flex:1">
                <option value="">— Product —</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (₹{{ $p->price }}, stock: {{ $p->inventory?->available_stock ?? 0 }})</option>
                @endforeach
            </select>
            <input type="number" name="quantities[]" value="1" min="1" class="form-input" style="width:80px" placeholder="Qty">
            <button type="button" class="btn-sm btn-danger remove-item">✕</button>
        </div>
        @endif
    </div>
    <button type="button" id="add-item" class="btn-secondary" style="margin-top:8px">+ Add product</button>

    <div style="margin-top:20px;display:flex;gap:10px">
        <button type="submit" class="btn-primary">{{ isset($order) ? 'Update Draft' : 'Create Draft' }}</button>
        <a href="{{ route('admin.mataji-orders.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>

<script>
const productOptions = `@foreach($products as $p)<option value="{{ $p->id }}">{{ addslashes($p->name) }} (₹{{ $p->price }}, stock: {{ $p->inventory?->available_stock ?? 0 }})</option>@endforeach`;

document.getElementById('add-item').addEventListener('click', function () {
    const row = document.createElement('div');
    row.className = 'order-item-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;align-items:center';
    row.innerHTML = `<select name="product_ids[]" class="form-input" style="flex:1"><option value="">— Product —</option>${productOptions}</select>
        <input type="number" name="quantities[]" value="1" min="1" class="form-input" style="width:80px">
        <button type="button" class="btn-sm btn-danger remove-item">✕</button>`;
    document.getElementById('items-container').appendChild(row);
    row.querySelector('.remove-item').addEventListener('click', () => row.remove());
});

document.querySelectorAll('.remove-item').forEach(btn => btn.addEventListener('click', () => btn.closest('.order-item-row').remove()));
</script>
