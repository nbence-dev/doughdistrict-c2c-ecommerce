<style>
    .review-form-card {
        background: var(--dd-surface-low);
    }

    .star-btn:focus {
        outline: none;
        box-shadow: none;
    }

    #review-comment:focus {
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(111, 70, 39, 0.2);
        outline: none;
    }
</style>

<div class="review-form-card rounded-4 p-4">
    <h5 class="fw-bold mb-4" style="color: var(--dd-on-surface);">Leave a Review</h5>

    <!-- Product mini-header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <?php if (!empty($product['image_url'])): ?>
        <img src="<?= htmlspecialchars($product['image_url']) ?>" class="rounded-3"
             style="width:64px;height:64px;object-fit:cover;flex-shrink:0;" alt="">
        <?php else: ?>
        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:64px;height:64px;background:var(--dd-surface);font-size:1.6rem;">🥐</div>
        <?php endif; ?>
        <div>
            <div class="fw-semibold small" style="color:var(--dd-on-surface);"><?= htmlspecialchars($product['name']) ?></div>
            <div class="small" style="color:var(--dd-outline);"><?= htmlspecialchars($product['shop_name']) ?></div>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>reviews/create">
        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

        <!-- Order selector -->
        <?php if (count($eligibleOrderIds) === 1): ?>
            <input type="hidden" name="order_id" value="<?= (int) $eligibleOrderIds[0] ?>">
        <?php else: ?>
        <div class="mb-4">
            <label class="form-label fw-semibold small" style="color:var(--dd-on-surface-var);">Select Order</label>
            <select name="order_id" class="form-select"
                    style="background:var(--dd-surface);border:1px solid var(--dd-outline-var);border-radius:.75rem;">
                <?php foreach ($eligibleOrderIds as $oid): ?>
                    <option value="<?= (int) $oid ?>">Order #<?= (int) $oid ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Star picker -->
        <div class="mb-4 text-center">
            <div class="fw-semibold small mb-3" style="color:var(--dd-on-surface-var);">How was your experience?</div>
            <div class="d-flex justify-content-center gap-1" id="star-picker">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <button type="button" class="star-btn border-0 bg-transparent p-1" data-value="<?= $i ?>">
                    <span class="material-symbols-outlined"
                          style="font-size:2.25rem;color:var(--dd-outline);transition:color .12s,font-variation-settings .12s;"
                          id="review-star-<?= $i ?>">star</span>
                </button>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="rating-input" value="0">
            <div class="small mt-2" id="rating-label" style="color:var(--dd-outline);min-height:1.25em;"></div>
        </div>

        <!-- Comment -->
        <div class="mb-4">
            <label class="form-label fw-semibold small" style="color:var(--dd-on-surface-var);" for="review-comment">
                Share your experience <span class="fw-normal">(optional)</span>
            </label>
            <textarea class="form-control" id="review-comment" name="comment" rows="4"
                      placeholder="What did you love about this product?"
                      style="background:var(--dd-surface);border:1px solid var(--dd-outline-var);border-radius:.75rem;resize:none;"></textarea>
        </div>

        <button type="submit" class="btn btn-dd-primary w-100 py-3 fw-bold" id="submit-review" disabled>
            Post Review
        </button>
    </form>
</div>

<script>
(function () {
    var labels = ['', 'Poor', 'Fair', 'Good', 'Great', 'Excellent'];
    var current = 0;

    function paint(n) {
        for (var i = 1; i <= 5; i++) {
            var star = document.getElementById('review-star-' + i);
            if (i <= n) {
                star.style.color = 'var(--dd-secondary)';
                star.style.fontVariationSettings = "'FILL' 1";
            } else {
                star.style.color = 'var(--dd-outline)';
                star.style.fontVariationSettings = "'FILL' 0";
            }
        }
    }

    document.querySelectorAll('.star-btn').forEach(function (btn) {
        btn.addEventListener('mouseenter', function () {
            paint(parseInt(this.dataset.value));
        });
        btn.addEventListener('mouseleave', function () {
            paint(current);
        });
        btn.addEventListener('click', function () {
            current = parseInt(this.dataset.value);
            document.getElementById('rating-input').value = current;
            document.getElementById('rating-label').textContent = labels[current];
            document.getElementById('submit-review').removeAttribute('disabled');
            paint(current);
        });
    });
})();
</script>
