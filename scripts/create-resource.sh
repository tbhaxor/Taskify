#!/bin/bash

if [[ -z "$1" ]]; then
    echo "[!] Base name is required"
    echo "Usage: $0 BASE_NAME [views|controllers|requests]"
    exit 1
fi

function make_views() {
    local view_names=('create' 'edit' 'index' 'show' 'delete')
    local base_name="$(echo "$1" | sed 's/\([A-Z]\)/-\L\1/g' | sed 's/^-//')s"
    for view_name in "${view_names[@]}"; do
        php artisan make:view "$base_name.$view_name"
    done
}

function make_controllers() {
    local controller_names=('Create' 'Edit' 'List' 'Show' 'Delete')
    for controller_name in "${controller_names[@]}"; do
        php artisan make:controller "${1}/${controller_name}${1}Controller" -i --test
    done
}

function make_requests() {
    local request_names=('Create' 'Edit' 'List' 'Show' 'Delete')
        for request_name in "${request_names[@]}"; do
            php artisan make:request "${1}/${request_name}${1}Request"
        done
}


if [[ -z "$2" ]]; then
    make_requests "$1"
    make_controllers "$1"
    make_views "$1"
else
    make_"$2" "$1"
fi
