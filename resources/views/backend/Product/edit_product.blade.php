<!-- Form fields -->
<input type="hidden" name="id" id="product_id">
<div class="form-group">
    <label for="product_name">Product Name</label>
    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" value="{{ $row->product_name }}" required>
</div>
<div class="form-group">
    <label for="SKU">SKU</label>
    <input type="text" class="form-control" id="SKU" name="SKU" required>
</div>
<div class="form-group">
    <label for="product_desc">Description</label>
    <textarea class="form-control" id="product_desc" name="product_desc" required></textarea>
</div>
<div class="form-group">
    <label for="product_price">Price</label>
    <input type="number" class="form-control" id="product_price" name="product_price" required>
</div>
<div class="form-group">
    <label for="product_image">Product Image</label>
    <input type="file" class="form-control-file" id="product_image" name="product_image">
</div>

<!-- Repeat this structure for each field -->