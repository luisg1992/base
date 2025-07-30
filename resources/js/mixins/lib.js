let exports = {}

exports.toCamelCase = (snakeStr) => {
    return snakeStr.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase())
}

exports.toSnakeCase = (str) => {
    return str.replace(/([A-Z])/g, '_$1').toLowerCase();
}

exports.formatDateToYMD = (date) => {
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');

    return `${yyyy}-${mm}-${dd}`;
}

exports.toSnakeCase = (str) => {
    return str.replace(/([A-Z])/g, '_$1').toLowerCase();
}

exports.generateUser = (name, lastName) => {
    if (!name || !lastName) return null;
    // Elimina espacios y convierte a minúsculas
    const firstLetterName = name.trim().charAt(0).toLowerCase();
    const lastNameClean = lastName.trim().toLowerCase();
    // Genera el usuario
    return firstLetterName + lastNameClean;
}

export default exports
