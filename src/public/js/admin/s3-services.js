async function s3upload(file) {
    const s3Meta = $('meta[name="s3-service"]')
    const token = s3Meta.attr('token');
    const presignedUrl = s3Meta.attr('presignedUrl');

    if(s3Meta && token && presignedUrl){

        try {
            fileType = file.type;
            fileContents = file;

            let data = JSON.stringify({
                "contentType": file.type
            });

            let config = {
                method: 'post',
                maxBodyLength: Infinity,
                url: presignedUrl,
                headers: {
                    'x-access-token': token,
                    'Content-Type': 'application/json'
                },
                data: data
            };

            let presignedPostUrlResponse = await axios.request(config)

            if (!presignedPostUrlResponse.status) {
                throw new Error('Failed to get presigned URL');
            }

            const presignedPostUrl = presignedPostUrlResponse.data;
            let key;
            let url;
            if (presignedPostUrl?.data?.result?.fields?.Key) {
                key = presignedPostUrl?.data?.result?.fields?.Key
            }
            if (presignedPostUrl?.data?.url) {
                url = presignedPostUrl?.data?.url
            }

            const formData = new FormData();
            Object.entries(presignedPostUrl?.data?.result?.fields).forEach(([k, v]) => {
                formData.append(k, v);
            });
            
            //Here appending file which we have to upload to S3
            formData.append('file', file);

            let config2 = {
                method: 'post',
                maxBodyLength: Infinity,
                url: presignedPostUrl?.data?.result?.url,
                headers: {
                    'Access-Control-Allow-Origin': '*',
                },
                data: formData
            }

            const response = await axios.request(config2);

            if (response.status == 200 || response.status == 204) {
                console.log('url: ', url)
                return url
            }
            return false

        } catch (error) {
            console.error(error);
            return false;
        }
    }
    return null;
}