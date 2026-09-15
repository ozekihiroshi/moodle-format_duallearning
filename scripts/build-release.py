#!/usr/bin/env python3
"""Build a deterministic Moodle Marketplace ZIP for format_duallearning."""

from __future__ import annotations

import re
import stat
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
BUILD = ROOT / "build"
EXCLUDED_TOP_LEVEL = {".git", ".github", ".gitattributes", ".gitignore", "build", "scripts"}
REQUIRED = {
    "duallearning/LICENSE",
    "duallearning/README.md",
    "duallearning/format.php",
    "duallearning/lang/en/format_duallearning.php",
    "duallearning/version.php",
}


def release_name() -> str:
    source = (ROOT / "version.php").read_text(encoding="utf-8")
    match = re.search(r"\$plugin->release\s*=\s*'([^']+)'", source)
    if not match:
        raise RuntimeError("Could not read $plugin->release from version.php")
    return match.group(1)


def included_files() -> list[Path]:
    files: list[Path] = []
    for path in ROOT.rglob("*"):
        relative = path.relative_to(ROOT)
        if relative.parts[0] in EXCLUDED_TOP_LEVEL:
            continue
        if path.is_file() and path.suffix != ".pyc" and "__pycache__" not in relative.parts:
            files.append(path)
    return sorted(files, key=lambda item: item.relative_to(ROOT).as_posix())


def main() -> None:
    BUILD.mkdir(exist_ok=True)
    destination = BUILD / f"moodle-format_duallearning-{release_name()}.zip"
    with zipfile.ZipFile(destination, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for source in included_files():
            relative = source.relative_to(ROOT).as_posix()
            info = zipfile.ZipInfo(f"duallearning/{relative}", (2026, 1, 1, 0, 0, 0))
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = (stat.S_IFREG | 0o644) << 16
            archive.writestr(info, source.read_bytes())

    with zipfile.ZipFile(destination) as archive:
        names = set(archive.namelist())
        roots = {name.split("/", 1)[0] for name in names}
        missing = REQUIRED - names
        if roots != {"duallearning"} or missing:
            raise RuntimeError(f"Invalid archive: roots={roots}, missing={sorted(missing)}")
    print(destination)


if __name__ == "__main__":
    main()
