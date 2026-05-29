class Offer {
    #priceCents;

    constructor({ id, name, price }) {
        this.id = 'offer_' + id; // Nadajemy unikalny identyfikator tekstowy
        this.rawId = Number.parseInt(id, 10); // Zachowujemy czyste ID numeryczne
        this.name = String(name);
        
        // Przeliczamy cenę z bazy (np. 14.50) na centy/grosze (1450)
        this.#priceCents = Number.parseFloat(price);
        this.category = 'Oferta Specjalna';
    }

    // Read-only cena w centach/groszach
    get price() {
        return this.#priceCents;
    }

    // Metoda pomocnicza, żeby checkout mógł wywołać tę samą funkcję co dla produktów
    applyDiscount() {
        return this.#priceCents;
    }
}