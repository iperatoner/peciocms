function is_integer(number) {
    parsed = parseInt(number);
    if (isNaN(parsed)) {
        return false;
    }
    else {
        return number == parsed && number.toString() == parsed.toString();
    }
} 