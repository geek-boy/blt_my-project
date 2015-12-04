#!/bin/sh
#
# Cloud Hook: features-revert
#
# Runs deployment tasks (PreviousNext approved).

DRUSH='/usr/local/bin/drush6'

site="$1"
target_env="$2"

# Registry rebuild.
$DRUSH @$site.$target_env dl registry_rebuild --yes
$DRUSH @$site.$target_env rr

# Update database.
$DRUSH @$site.$target_env updatedb --yes

# Features revert.
$DRUSH @$site.$target_env fra --yes

# Clear the cache.
$DRUSH @$site.$target_env cc all --yes

# Clear drush cache.
$DRUSH @$site.$target_env cc drush --yes
