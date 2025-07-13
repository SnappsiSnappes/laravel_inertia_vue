export default class ImageHelper {


    /**
     * Метод возвращает объект c width, height, url 
     * 
     * использовать если изначально нет width, height а есть только
     * 
     * url на картинку
     * @param {String} ImageUrl 
     * @returns объект с доп. свойствами width, height, url
     */
    static GetImageWithSizes = async (ImageUrl) => {
        const img = new Image();
        img.src = ImageUrl;

        // Ждем загрузки изображения
        await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
        });

        // Обновляем реактивный массив с данными изображения
        return {
                url: ImageUrl,
                width: img.naturalWidth, // Реальная ширина изображения
                height: img.naturalHeight, // Реальная высота изображения
            }

    };

    /**
    * Метод возвращает список c объектами с доп полями width, height, url 
     * 
     * использовать если изначально у файлов нет width, height а есть только
     * 
     * url на картинку
     * @param {Array} files 
     * @returns {Array}Список с объектами на каждый файл с доп свойствами: width, height, url
     */
    static GetImagesWithSizes = async ( files)=>{
        if (!Array.isArray(files)) {
            console.warn("Files prop is not an array. Using empty array as fallback.");
            return;
        }
    
        const loadedImages = await Promise.all(
            files.map(async (file) => {
                const img = new Image();
                img.src = file.url;
    
                // Ждем загрузки изображения
                await new Promise((resolve, reject) => {
                    img.onload = resolve;
                    img.onerror = reject;
                });
    
                // Возвращаем данные изображения с реальными размерами
                return {
                    ...file,
                    width: img.naturalWidth, // Реальная ширина изображения
                    height: img.naturalHeight, // Реальная высота изображения
                };
            })
        );
    
        // Обновляем реактивный массив
        return loadedImages;
    }
}
