class Product {
    // priceCents stored privately to be read-only
    #priceCents;

    constructor({ id, name, picture, category, price_cents, discount_percent = null, stock = 0 }) {
        this.id = Number.parseInt(id, 10);
        this.name = String(name);
        this.picture = picture;
        this.category = String(category);
        this.#priceCents = Number.parseInt(price_cents, 10);
        this._discount = discount_percent === null ? 0 : Number.parseInt(discount_percent, 10);
        this.stock = Number.parseInt(stock, 10);
        this.discount = this._discount; // enforce bounds
    }

    // Read-only price (in cents)
    get price() {
        return this.#priceCents;
    }

    // discount property (0-100)
    get discount() {
        return this._discount;
    }

    set discount(value) {
        let v = Number.parseInt(value || 0, 10);
        if (Number.isNaN(v)) v = 0;
        if (v < 0) v = 0;
        if (v > 100) v = 100;
        this._discount = v;
    }

    // Apply discount and return discounted price in cents
    applyDiscount() {
        const discounted = Math.round(this.price * (100 - this.discount) / 100);
        return discounted;
    }

    // Is product available
    isAvailable() {
        return Number.isFinite(this.stock) && this.stock > 0;
    }
}

// expose globally
window.Product = Product;