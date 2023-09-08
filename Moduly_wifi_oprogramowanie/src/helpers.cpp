#include "helpers.h"

short findchar(const unsigned char *str, const unsigned short len, const unsigned char deli)
{
    unsigned short i;
    for (i = 0; i < len && str[i] != deli; i++)
        ;
    if (i < len)
        return i; // return position if found,else return -1
    return -1;
}
