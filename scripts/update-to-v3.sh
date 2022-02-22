#!/usr/bin/env bash

find "$@" -type f -print0 | xargs -0 sed -i -r \
  -e 's/wmmodel/entity_model/g' \
  -e 's/entity_model_factory/wmmodel_factory/g'

find "$@" -type f -iname "wmmodel.settings.yml" -exec rename 's/wmmodel/entity_model/' '{}' \;
