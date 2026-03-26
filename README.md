# SmartShop — Dynamic Pricing & Reactive Cart Simulator

A high-performance **Laravel** e-commerce simulator demonstrating advanced pricing logic: real-time cart reactivity, coupon validation, premium benefits, and dynamic weekend surges — all powered by a clean Service-Layer architecture.

---

## ⚡ Key Features

- **Reactive Cart UI**: Live, AJAX-powered pricing breakdown (Apply/Remove coupons without refreshing).
- **Multi-Layer Discounting**: Cascading logic for Item (Qty ≥ 3), Cart (Subtotal ≥ ₹1,000), and Coupons.
- **Premium Member Benefits**: Extra **5% discount** applied to the final calculated total.
- **Inventory Guard**: Atomic inventory decrements and stock-awareness across all operations.
- **Dynamic Weekend Surge**: Automatic **+10% surcharge** on Saturdays and Sundays.

---

## 🚀 Quick Setup

1. **Install Dependencies**:
   ```bash
   composer install
   ```

   ```bash
   npm install
   ```
   
2. **Environment**:
   ```bash
   cp .env.example .env
   ```

    ```bash
   php artisan key:generate
   ```
    
3. **Database**: Create `smart_coupon` in MySQL and update `.env`. Then run:
   ```bash
   php artisan migrate:fresh --seed
   ```

   ```bash
   npm run build
   ```
   
4. **Launch**:
   ```bash
   php artisan serve
   ```

   Visit: **http://127.0.0.1:8000**

---

## 💎 Test Accounts & Coupons

| Account              | Password     | Benefit              |
| :------------------- | :----------- | :------------------- |
| `premium@test.com` | `password` | ★ 5% Extra Discount |
| `regular@test.com` | `password` | Standard Pricing     |

**Active Coupons**: `SAVE10` (10%), `FLAT200` (₹200 off), `BIGSALE` (20%, cap ₹300).

---

## 📊 Pricing Strategy (Pipeline)

| Rule                    | Logic                 | Effect                    |
| :---------------------- | :-------------------- | :------------------------ |
| **Weekend Surge** | Saturday or Sunday    | **+10%** Subtotal   |
| **Item Discount** | Quantity ≥ 3         | **-10%** off item   |
| **Cart Discount** | Subtotal ≥ ₹1,000   | **-₹100** flat     |
| **Coupons**       | Valid code            | **Varies**          |
| **Premium**       | User is premium       | **-5%** remaining   |
| **Tax (GST)**     | All orders            | **+5%**             |
| **Delivery**      | Pre-tax total < ₹500 | **₹50**, else FREE |

---

## 🏗️ Architecture

- **`PricingService`**: The master logic controller (9-step pipeline).
- **`CartService`**: Session-to-Database synchronized cart manager.
- **`CouponService`**: Validation and consumption logic.
- **`Middleware`**: Transparent premium status injection.

---

*SmartShop — Built with Laravel | Dynamic Pricing & Order Simulator*
