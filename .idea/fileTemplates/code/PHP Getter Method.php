/**
 * Method to get property ${NAME}
 *
 * @return  ${TYPE_HINT}
 */
public ${STATIC} function get${NAME}(): ${TYPE_HINT}
{
#if (${STATIC} == "static")
    return static::$${FIELD_NAME};
#else
    return $this->${FIELD_NAME};
#end
}

