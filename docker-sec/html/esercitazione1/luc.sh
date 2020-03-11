#!/bin/bash
cat $1 | while read line
do
  IFS='. ' read -a array <<< "$line" 
  
  len = ${#array[@]}
  if[ $len -eq 2 ]; then 
    printf "stocazzo"
  fi
done

