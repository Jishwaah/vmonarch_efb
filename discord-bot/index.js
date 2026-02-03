import axios from 'axios';
import { Client, GatewayIntentBits, Partials } from 'discord.js';
import fs from 'node:fs';
import path from 'node:path';

const configPath = path.join(process.cwd(), 'config.json');
let fileConfig = {};

if (fs.existsSync(configPath)) {
    try {
        fileConfig = JSON.parse(fs.readFileSync(configPath, 'utf-8'));
    } catch (error) {
        console.error('Failed to parse config.json:', error.message);
        process.exit(1);
    }
}

const DISCORD_BOT_TOKEN = fileConfig.discord_bot_token || process.env.DISCORD_BOT_TOKEN;
const WEBHOOK_URL = fileConfig.webhook_url || process.env.WEBHOOK_URL;
const WEBHOOK_SECRET = fileConfig.webhook_secret || process.env.WEBHOOK_SECRET;

if (!DISCORD_BOT_TOKEN || !WEBHOOK_URL || !WEBHOOK_SECRET) {
    console.error('Missing configuration. Provide config.json or env vars for DISCORD_BOT_TOKEN, WEBHOOK_URL, WEBHOOK_SECRET.');
    process.exit(1);
}

const client = new Client({
    intents: [
        GatewayIntentBits.Guilds,
        GatewayIntentBits.DirectMessages,
        GatewayIntentBits.MessageContent,
    ],
    partials: [Partials.Channel],
});

client.on('ready', () => {
    console.log(`Logged in as ${client.user.tag}`);
});

client.on('messageCreate', async (message) => {
    if (message.author.bot) return;
    if (message.guildId) return;

    const payload = {
        discord_user_id: message.author.id,
        author: message.author.username,
        message: message.content,
    };

    try {
        await axios.post(WEBHOOK_URL, payload, {
            headers: {
                'X-Discord-Webhook-Token': WEBHOOK_SECRET,
            },
            timeout: 10000,
        });
        const now = new Date();
        const hh = String(now.getUTCHours()).padStart(2, '0');
        const mm = String(now.getUTCMinutes()).padStart(2, '0');
        const ss = String(now.getUTCSeconds()).padStart(2, '0');
        await message.channel.send(`MESSAGE RECEIVED ${hh}:${mm}:${ss} UTC`);
        await message.react('?');
    } catch (error) {
        console.error('Failed to post webhook:', error.message);
        await message.react('?');
    }
});

client.login(DISCORD_BOT_TOKEN);
