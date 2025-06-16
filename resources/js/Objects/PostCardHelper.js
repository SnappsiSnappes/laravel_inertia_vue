export class PostCardHelper {
    static SeparateContentAndImage(ParsedBody) {
        const Content = [];
        const Image = [];
    
        for (let index = 0; index < ParsedBody.blocks.length; index++) {
            const element = ParsedBody.blocks[index];
            switch (element.type) {
                case "paragraph":
                    Content.push(element);
                    break;
                case "image":
                    Image.push(element);
                    break;
                default:
                    Content.push(element);
                    break;
            }
        }

        return {Content,Image};
    }
}
