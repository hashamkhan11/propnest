# Deployment

PropNest ships as one Docker image (nginx + PHP-FPM). The same image runs the free live demo and a real production install; only the environment variables differ.

## Free live demo on Render

Render's free web service runs the image with no persistent disk. PropNest uses that on purpose: with `SEED_DEMO=true` it creates a new SQLite database and seeds the demo marketplace every time the service starts. Visitors can change anything, and the demo resets itself.

### Steps

1. Sign in to [Render](https://render.com) with your GitHub account.
2. Click **New > Blueprint** and pick the `propnest` repository. Render reads [`render.yaml`](../render.yaml).
3. Fill in the values Render asks for:

   | Variable | Value |
   |---|---|
   | `APP_URL` | `https://propnest.onrender.com` (use the URL Render shows for your service) |
   | `APP_KEY` | Output of `php artisan key:generate --show`, or leave empty and a key is generated on each start |
   | Stripe and Google keys | Leave empty, or see below |

4. Click **Apply**. The first build takes a few minutes. When the service is live, open its URL and sign in with a demo account from the README.

If Render gives the service a different URL than the one you entered, update `APP_URL` under **Environment** and the service restarts.

### Custom domain (optional)

1. In the service's **Settings > Custom Domains**, add a subdomain such as `demo.example.com`.
2. At your DNS provider, add a `CNAME` record from that subdomain to the service's `onrender.com` host. On Cloudflare, set it to **DNS only** (grey cloud) so Render can issue the certificate.
3. When Render shows **Certificate Issued**, set `APP_URL` to the new address.

Some internet providers cache the "domain not found" answer for up to an hour. If the domain works on mobile data but not at home, wait or switch the browser to secure DNS.

### What to expect

- The free service sleeps after 15 minutes without traffic. The next visit wakes it up in about a minute, then reseeds the demo (around 15 seconds).
- Every restart or redeploy is a fresh demo. Nothing a visitor does is kept.
- Free services get 750 instance hours a month, enough for one service running all the time.

### Stripe in test mode (optional)

1. In the Stripe dashboard, switch to **Test mode** and copy the publishable and secret keys into `STRIPE_KEY` and `STRIPE_SECRET`.
2. Under **Developers > Webhooks**, add an endpoint:
   - URL: `https://<your-app>.onrender.com/api/stripe/webhook`
   - Events: `checkout.session.completed`, `checkout.session.expired`
3. Copy the endpoint's signing secret into `STRIPE_WEBHOOK_SECRET`.

Pay with the test card `4242 4242 4242 4242`, any future date and any CVC.

### Google sign-in (optional)

In Google Cloud Console, add `https://<your-app>.onrender.com/auth/google/callback` as an authorized redirect URI, then set `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` and `GOOGLE_REDIRECT_URI`.

## Other hosts

Any platform that builds a Dockerfile works the same way. The container listens on port `8080` and `/up` is the health check. Set the variables from `render.yaml`.

To run the image on your own machine:

```bash
docker compose up --build
```

## Real production install

The demo settings trade durability for zero cost. For real users, change these:

| Concern | Demo | Production |
|---|---|---|
| Database | SQLite, rebuilt on start | MySQL or PostgreSQL (`DB_CONNECTION`, `DB_HOST`, ...) |
| Seed data | `SEED_DEMO=true` | Unset. Run `php artisan db:seed --force` once, with `ADMIN_PASSWORD` set |
| `APP_KEY` | Optional | Required and fixed, or sessions and encrypted data break on restart |
| Uploaded photos | Container disk | A persistent disk or S3 (`FILESYSTEM_DISK`, `AWS_*`) |
| Queue | `QUEUE_CONNECTION=sync` | `database` or `redis`, with a worker running `php artisan queue:work` |
| Scheduler | Not running | `php artisan schedule:work` in a second process, or `schedule:run` every minute from cron |
| Mail | `MAIL_MAILER=log` | A real mail provider |
| Stripe | Test keys | Live keys and a live webhook endpoint |

The scheduler expires featured listings, subscriptions and stale checkouts, so it must run in production.

The app trusts the `X-Forwarded-*` headers from the platform's load balancer, so it generates `https` URLs behind Render, Fly.io, Railway and similar hosts.
