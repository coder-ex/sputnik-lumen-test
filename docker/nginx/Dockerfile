FROM nginx

# Install tools required for build stage
RUN apt-get update && apt-get install -fyqq \
    nmap net-tools tcpdump sudo nano mc

#ADD ./default.conf /etc/nginx/conf.d/default.conf

#ADD ./lp.conf /etc/nginx/conf.d/lp.conf
ADD ./school.conf /etc/nginx/conf.d/crm.conf

WORKDIR /var/www/html
