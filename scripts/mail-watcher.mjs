// Local-dev only: polls Mailpit's API and opens new messages in the browser.
// Not used in production; nothing here is loaded by the app.
import { exec } from 'node:child_process';
import { readFile, writeFile, mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const MAILPIT_URL = process.env.MAILPIT_URL ?? 'http://127.0.0.1:8025';
const POLL_INTERVAL_MS = 2000;

const projectRoot = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const stateFile = path.join(projectRoot, 'storage', '.mail-watcher-state.json');

function log(message) {
    console.log(`[mail-watcher] ${new Date().toLocaleTimeString()} ${message}`);
}

async function readLastSeenId() {
    try {
        const raw = await readFile(stateFile, 'utf8');
        return JSON.parse(raw).lastSeenId ?? null;
    } catch {
        return null;
    }
}

async function writeLastSeenId(id) {
    await mkdir(path.dirname(stateFile), { recursive: true });
    await writeFile(stateFile, JSON.stringify({ lastSeenId: id }), 'utf8');
}

function openInBrowser(url) {
    const platform = process.platform;
    const command =
        platform === 'win32'
            ? `start "" "${url}"`
            : platform === 'darwin'
              ? `open "${url}"`
              : `xdg-open "${url}"`;

    exec(command, { shell: platform === 'win32' ? 'cmd.exe' : '/bin/sh' }, (err) => {
        if (err) log(`failed to open browser: ${err.message}`);
    });
}

async function fetchNewestMessage() {
    const response = await fetch(`${MAILPIT_URL}/api/v1/messages?limit=1`);
    if (!response.ok) {
        throw new Error(`Mailpit API responded ${response.status}`);
    }
    const data = await response.json();
    return data.messages?.[0] ?? null;
}

async function poll(lastSeenId) {
    const newest = await fetchNewestMessage();
    if (!newest) return lastSeenId;

    if (lastSeenId === null) {
        // First run: establish a baseline without opening existing mail.
        await writeLastSeenId(newest.ID);
        log(`baseline set, watching for mail after "${newest.Subject}"`);
        return newest.ID;
    }

    if (newest.ID !== lastSeenId) {
        log(`new mail: "${newest.Subject}" -> opening`);
        openInBrowser(`${MAILPIT_URL}/view/${newest.ID}`);
        await writeLastSeenId(newest.ID);
        return newest.ID;
    }

    return lastSeenId;
}

async function main() {
    log(`watching ${MAILPIT_URL} every ${POLL_INTERVAL_MS}ms (Ctrl+C to stop)`);
    let lastSeenId = await readLastSeenId();

    setInterval(() => {
        poll(lastSeenId)
            .then((id) => {
                lastSeenId = id;
            })
            .catch((err) => log(`poll failed: ${err.message}`));
    }, POLL_INTERVAL_MS);
}

main();
