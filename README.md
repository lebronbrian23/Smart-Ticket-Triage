# Smart Ticket Triage & Dashboard

A Laravel 11 + Vue 3 (Options API) SPA for managing support tickets with AI-assisted classification.

---

## 🚀 Setup (≤ 10 steps)

1. **Clone the repo**
   ```bash
   git clone https://github.com/lebronbrian23/smart-ticket-triage.git
   cd smart-tickets
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JS dependencies**
   ```bash
   npm install
   ```
4. **Copy env file & set secrets**
   ```bash
   cp .env.example .env
   ```
    Update DB credentials , `OPENAI_API_KEY` , `OPENAI_ORGANIZATION` in .env.
    
5. **SQLite Option**
    Instead of MySQL/Postgres, you can use SQLite for quick local setup:
   ```bash 
   
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/smart-tickets/database/database.sqlite
   # and
   OPENAI_CLASSIFY_ENABLED=true
   OPENAI_API_KEY=key
   OPENAI_ORGANIZATION=org
   ```
6. **Then create the database file:**
   ```bash
   touch database/database.sqlite
   ```

7. **Generate app key**
   ```bash
   php artisan key:generate
   ```

8. **Run migrations & seed**
   ```bash
   php artisan migrate --seed
   ```

9. **Build frontend assets**
   ```bash
   npm run dev     # for local dev
   # or
   npm run build   # for production
   ```

10. **Run Laravel queue worker** (for async classification)
   ```bash
   php artisan queue:work
   ```

11. **Start server**
   ```bash
   php artisan serve
   ```
   Visit [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 📊 Features
- Ticket CRUD with internal notes
- AI-powered ticket classification (queued job)
- Dashboard with status/category stats & charts
- Dark/light theme toggle (persisted in localStorage)
- BEM-based CSS (no frameworks)

---

## ⚙️ Assumptions & Trade-offs

- **Options API only**: enforced for clarity/readability in small teams, at the expense of Composition API flexibility.
- **No CSS frameworks**: hand-rolled BEM styling → more control, but slower UI work vs Tailwind/Bootstrap.
- **AI Classification**: mocked or proxied to OpenAI; classification is async via Laravel jobs, so there’s polling in the frontend.
- **Search**: server-side filter only on `subject`, `description`, `status` and `category` fields.
- **Single Vite build**: SPA is bundled into `/public/build` and served by Laravel.

---

## ⏳ What I’d do with more time
- Add **auth** & per-user ticket ownership.
- Improve **classification service** with retries + caching.
- Add **tests**: PHPUnit + Vue component tests.
- Better **debounced search** with highlighted query matches.
- CI/CD pipeline with GitHub Actions for lint/test/deploy.
- Documentation
- Tests

---

## 📂 Tech Stack
- **Backend**: Laravel 11, MySQLite, Queue worker
- **Frontend**: Vue 3 (Options API), Vite, Chart.js, Axios
- **Styling**: Vanilla CSS with BEM
- **AI**: OpenAI API integration (configurable via `.env`)

---

## 🧪 API Testing with Postman

1. Import the provided Postman **collection**:
    - `Smart Ticket Triage.postman_collection.json`
   ```json
   {
        "info": {
            "name": "Smart Ticket Triage API",
            "_postman_id": "f3c43d1e-abc1-42d7-bb23-98b1f8f12345",
            "description": "Postman collection for testing Smart Ticket Triage backend (Laravel).",
            "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
        },
        "item": [
            {
                "name": "List Tickets",
                "request": {
                    "method": "GET",
                    "header": [],
                    "url": {
                        "raw": "{{base_url}}/api/tickets",
                        "host": ["{{base_url}}"],
                        "path": ["api", "tickets"]
                    }
                }   
            },
            {
                "name": "Create Ticket",
                "request": {
                "method": "POST",
                "header": [
                { "key": "Content-Type", "value": "application/json" }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n  \"subject\": \"Login issue\",\n  \"body\": \"I cannot log into my account with correct credentials.\"\n}"
                },
                "url": {
                    "raw": "{{base_url}}/api/tickets",
                    "host": ["{{base_url}}"],
                    "path": ["api", "tickets"]
                }
                }
            },
            {
            "name": "Show Ticket",
                "request": {
                "method": "GET",
                "url": {
                    "raw": "{{base_url}}/api/tickets/:id",
                        "host": ["{{base_url}}"],
                        "path": ["api", "tickets", ":id"]
                    }
                }
            },
            {
                "name": "Update Ticket",
                "request": {
                    "method": "PATCH",
                    "header": [
                        { "key": "Content-Type", "value": "application/json" }
                    ],
                    "body": {
                        "mode": "raw",
                        "raw": "{\n  \"status\": \"resolved\",\n  \"note\": \"Checked and fixed by support agent.\"\n}"
                    },
                    "url": {
                        "raw": "{{base_url}}/api/tickets/:id",
                        "host": ["{{base_url}}"],
                        "path": ["api", "tickets", ":id"]
                    }
                }
            },
            {
                "name": "Classify Ticket",
                "request": {
                    "method": "POST",
                    "url": {
                        "raw": "{{base_url}}/api/tickets/:id/classify",
                        "host": ["{{base_url}}"],
                        "path": ["api", "tickets", ":id", "classify"]
                    }
                }
            },
            {
                "name": "Get Stats",
                "request": {
                    "method": "GET",
                    "url": {
                        "raw": "{{base_url}}/api/stats",
                        "host": ["{{base_url}}"],
                        "path": ["api", "stats"]
                    }
                }
            }
        ],
        "variable": [
            {
                "key": "base_url",
                "value": "http://127.0.0.1:8000"
            }
        ]
    }

   

2. In Postman, select the environment **Smart Ticket Triage Local**.

3. The collection includes:
    - `GET /tickets` → list tickets (with optional `q` search param)
    - `POST /tickets` → create a new ticket
    - `GET /tickets/:id` → show a ticket
    - `PATCH /tickets/:id` → update ticket status/category/note
    - `POST /tickets/:id/classify` → queue AI classification
    - `GET /stats` → fetch dashboard statistics

4. **Auto-saving ticket IDs**: when creating a ticket, the Postman test script stores the new `id` into the environment variable `ticket_id`, so you can immediately test `GET`, `PATCH`, and `classify` endpoints without copy-pasting.

---
