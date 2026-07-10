#!/usr/bin/env python3
"""
Генератор статусной плашки (badge) для API v1.0
Стиль: как у GitHub Actions / Shields.io
Данные из документации Mint Scripts Studio
"""

import datetime
import os
import sys
from typing import Dict, Any

# === КОНФИГУРАЦИЯ ===
CONFIG: Dict[str, Any] = {
    "api_version": "v1.0",
    "studio_name": "Mint Scripts Studio",
    "status": "✅ ONLINE",           # ✅ ONLINE / ⚠️ DEGRADED / ❌ OFFLINE
    "uptime": "99.9%",
    "last_checked": datetime.datetime.now().strftime("%Y-%m-%d %H:%M UTC"),
    "endpoints": {
        "play": "/playGame.do",
        "actions": "/gameActions.do",
        "callback": "your-callback-url",
    },
    "providers": ["pragmatic", "hacksaw"],
    "currencies": ["USD", "EUR"],
}


def generate_svg_badge(config: Dict[str, Any]) -> str:
    """Генерирует SVG-плашку с информацией об API."""
    
    # Цвета статуса
    status_colors = {
        "✅ ONLINE": "#2ea44f",
        "⚠️ DEGRADED": "#d29922",
        "❌ OFFLINE": "#da3633",
    }
    
    status_color = status_colors.get(config["status"], "#2ea44f")
    api_version = config["api_version"]
    studio = config["studio_name"]
    endpoints = config["endpoints"]
    
    svg = f'''<svg xmlns="http://www.w3.org/2000/svg" width="520" height="120" viewBox="0 0 520 120">
    <defs>
        <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#0f1a22;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#1a2f3a;stop-opacity:1" />
        </linearGradient>
        <filter id="shadow" x="-5%" y="-5%" width="110%" height="110%">
            <feDropShadow dx="0" dy="2" stdDeviation="4" flood-color="#000" flood-opacity="0.3"/>
        </filter>
    </defs>

    <!-- Прямоугольник -->
    <rect width="520" height="120" rx="12" fill="url(#bg)" stroke="#2d5b6b" stroke-width="1" filter="url(#shadow)"/>

    <!-- Логотип / иконка -->
    <circle cx="40" cy="40" r="18" fill="#4dc9e8" opacity="0.2"/>
    <circle cx="40" cy="40" r="12" fill="none" stroke="#4dc9e8" stroke-width="2"/>
    <text x="40" y="46" font-family="Arial, sans-serif" font-size="16" fill="#4dc9e8" text-anchor="middle" font-weight="bold">M</text>

    <!-- Название студии -->
    <text x="70" y="30" font-family="Arial, sans-serif" font-size="14" fill="#b3e8f5" font-weight="600">{studio}</text>

    <!-- Версия API -->
    <rect x="70" y="38" width="70" height="22" rx="11" fill="#142f3a" stroke="#2d5b6b" stroke-width="1"/>
    <text x="105" y="53" font-family="Arial, sans-serif" font-size="11" fill="#89d4ea" text-anchor="middle" font-weight="600">API {api_version}</text>

    <!-- Статус -->
    <rect x="155" y="38" width="120" height="22" rx="11" fill="{status_color}" opacity="0.15"/>
    <text x="215" y="53" font-family="Arial, sans-serif" font-size="12" fill="{status_color}" text-anchor="middle" font-weight="700">{config["status"]}</text>

    <!-- Uptime -->
    <text x="340" y="30" font-family="Arial, sans-serif" font-size="11" fill="#7bb8cc">uptime</text>
    <text x="340" y="48" font-family="Arial, sans-serif" font-size="18" fill="#c3e2ef" font-weight="700">{config["uptime"]}</text>

    <!-- Endpoints -->
    <text x="30" y="85" font-family="Arial, sans-serif" font-size="10" fill="#5f8f9f">ENDPOINTS</text>
    <text x="30" y="100" font-family="Arial, sans-serif" font-size="10" fill="#9ac9db">▶ {endpoints.get("play", "N/A")}</text>
    <text x="160" y="100" font-family="Arial, sans-serif" font-size="10" fill="#9ac9db">▶ {endpoints.get("actions", "N/A")}</text>
    <text x="310" y="100" font-family="Arial, sans-serif" font-size="10" fill="#9ac9db">▶ {endpoints.get("callback", "N/A")}</text>

    <!-- Провайдеры -->
    <text x="430" y="85" font-family="Arial, sans-serif" font-size="10" fill="#5f8f9f">PROVIDERS</text>
    <text x="430" y="100" font-family="Arial, sans-serif" font-size="10" fill="#9ac9db">{" | ".join(config["providers"])}</text>

    <!-- Время последней проверки -->
    <text x="30" y="115" font-family="Arial, sans-serif" font-size="8" fill="#4f6f7f">last check: {config["last_checked"]}</text>

    <!-- Currencies -->
    <text x="430" y="115" font-family="Arial, sans-serif" font-size="8" fill="#4f6f7f">currencies: {" | ".join(config["currencies"])}</text>
</svg>'''
    return svg


def save_badge(svg_content: str, output_path: str = "api_status_badge.svg") -> None:
    """Сохраняет SVG-файл."""
    os.makedirs(os.path.dirname(output_path) or ".", exist_ok=True)
    with open(output_path, "w", encoding="utf-8") as f:
        f.write(svg_content)
    print(f"✅ SVG-плашка сохранена: {output_path}")


def generate_readme_badge_markdown(badge_url: str) -> str:
    """Генерирует Markdown для вставки в README.md."""
    return f'''
## 📊 Статус API

![API Status]({badge_url})

> **{CONFIG["studio_name"]}** · API {CONFIG["api_version"]} · Статус: {CONFIG["status"]} · Uptime: {CONFIG["uptime"]}
>
> - **play**: `{CONFIG["endpoints"]["play"]}`
> - **actions**: `{CONFIG["endpoints"]["actions"]}`
> - **providers**: {", ".join(CONFIG["providers"])}
'''

def main():
    """Основная функция."""
    print("🔄 Генерация статусной плашки для API...")
    
    # 1. Генерируем SVG
    svg = generate_svg_badge(CONFIG)
    
    # 2. Сохраняем файл
    save_badge(svg, "api_status_badge.svg")
    
    # 3. Показываем пример вставки в README
    print("\n📝 Пример вставки в README.md:")
    print("-" * 50)
    print(generate_readme_badge_markdown("api_status_badge.svg"))
    print("-" * 50)
    
    # 4. Показываем путь к файлу
    abs_path = os.path.abspath("api_status_badge.svg")
    print(f"\n📁 Файл создан: {abs_path}")
    print("💡 Используйте его в README.md как изображение или на веб-странице")


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n⏹️ Прервано пользователем")
        sys.exit(0)
    except Exception as e:
        print(f"❌ Ошибка: {e}")
        sys.exit(1)
