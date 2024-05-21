<!-- Form fields -->
<input type="hidden" name="id" id="product_id">
<div class="form-group">
    <label for="product_name">Product Name</label>
    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" value="{{ $row->product_name }}" required>
</div>
<div class="form-group">
    <label for="SKU">SKU</label>
    <input type="text" class="form-control" id="SKU" name="SKU" placeholder="Enter product SKU" value="{{ $row->SKU }}" required>
</div>
<div class="form-group">
    <label for="product_desc">Description</label>
    <textarea class="form-control" id="product_desc" name="product_desc" placeholder="Enter product description (optional)" value="{{ $row->product_desc }}"></textarea>
</div>
<div class="form-group">
    <label for="expired_date">Expired date</label>
    <input type="text" class="form-control" id="expired_date" name="expired_date" placeholder="Enter product expired date" value="{{ $row->expired_date }}">
</div>
<div class="form-group">
    <label for="UOM">Unit of Measurement (UOM)</label>
    <input type="text" class="form-control" id="UOM" name="UOM" placeholder="Enter product UOM" value="{{ $row->UOM }}">
</div>
<div class="form-group">
    <label for="weight_per_unit">Weight per unit (kg)</label>
    <input type="text" class="form-control" id="weight_per_unit" name="weight_per_unit" placeholder="Enter product weight per unit in kg" value="{{ $row->weight_per_unit }}">
</div>
<div class="form-group">
    <label for="product_img">Product Image</label>
    <input type="file" class="form-control-file" id="product_img" name="Img">
</div>
