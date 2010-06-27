#!/bin/bash
#TARGET=/home/james/Downloads/aa
for INPUT in *.[iI][sS][oO]; do
  nice wbfs_file -l d2 -x 1 "${INPUT}" 
#"${TARGET}"
done
exit
