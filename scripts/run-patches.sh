#!/bin/sh

base_dir="$(git rev-parse --show-toplevel)"
for patch_file in "$base_dir"/patches/*.patch; do
    target_file=$(awk '{print $2}' < "$patch_file" | sed '2q;d')
    patch -p0 --forward "$target_file" "$patch_file" || true
done
