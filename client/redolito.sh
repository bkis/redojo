#!/bin/bash

#    #######################
#    #### redolito      ####
#    #### client script ####
#    #######################

echo "[ redolito ]"

# define variables
config_file="redolito.config"
lock_file="redolito.lock"
temp_dir="temp"
list_file="redolito.list"

# get script directory
script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# cd into script directory
# (in case it's not the current working directory)
cd "$script_dir"

# check presence of config file
[ ! -f "$config_file" ] && echo "[ERROR] Config file '$config_file' not found :(" && exit

# if lock file is present, exit
[ -f "$lock_file" ] && echo "[ERROR] Lock file present. script might already be running." && exit

# else, create lock file
touch "$lock_file"

# get config variables
source "redolito-client.config"
server_jobs_list="$server_dir/jobs/redolito.jobs"

# create temporary directory
mkdir -p "$temp_dir" || echo "[ERROR] Could not create temporary directory: $temp_dir"

# create target directory
mkdir -p "$target_dir" || echo "[ERROR] Could not create missing target directory: $target_dir"

#    #######################
#    # start download jobs #
#    #######################

# download list of planned downloads from server
echo "Fetching jobs list from server: $server_jobs_list"
wget -O "$list_file" ${http_user:+--http-user=$http_user} ${http_pw:+--http-password=$http_pw} "$server_jobs_list" -q
# ...and check if it's there...
[ ! -f "$list_file" ] && echo "[ERROR] Jobs list could not be downloaded :(" && exit

# process downloads list
echo "Running download jobs ..."
while IFS= read -r line; do
    # skip line if it's too short
    [ ${#line} -gt 5 ] || continue
    # download ghost file
    job_file="$temp_dir/$line"
    wget -O "$job_file" ${http_user:+--http-user=$http_user} ${http_pw:+--http-password=$http_pw} "$server_dir/jobs/$line" -q
    # ...and check if it's there...
    [ ! -f "$job_file" ] && echo "    --> Job file '$job_file' could not be downloaded :(" && continue
    # load ghost file data
    source "$job_file"
    # prepare dl job vars
    # dl_name=    <-- is loaded from ghost file
    # dl_url=    <-- is loaded from ghost file
    dl_targetfile="$target_dir/$dl_name"

    if [ -f "$dl_targetfile" ]
    then
	    echo "    --> Skipping: \"$dl_name\" (already exists!)"
        continue
    else
	    echo "    --> Downloading: \"$dl_name\" (from: ${dl_url:0:24}...)"
        wget -O "$dl_targetfile" ${http_user:+--http-user=$http_user} ${http_pw:+--http-password=$http_pw} "$dl_url" -q
    fi
done < "$list_file"

# delete temp files
rm -f -r "$temp_dir"

# delete jobs list file
rm -f "$list_file"

# delete lock file
rm -f "$lock_file"

# done
echo "Done."