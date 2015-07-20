./getupstream.sh
set -ex
echo "upstream =+ develop -> release -> master"

set +ex
echo "git push --all origin -u  to push all changes to remote"
