/**
 * Method to get property ${NAME}
 *
 * @return  ${TYPE_HINT}
 */
public ${STATIC} function ${GET_OR_IS}${NAME}()
{
#if (${STATIC} == "static")
    return static::$${FIELD_NAME};
#else
    return $this->${FIELD_NAME};
#end
}

