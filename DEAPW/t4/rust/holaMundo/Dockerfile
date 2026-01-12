# Use a Rust base image with Cargo installed
FROM rust:1.78.0 AS builder

# Set the working directory inside the container
WORKDIR /usr/src/app

# Copy the Cargo.toml and Cargo.lock files
COPY Cargo.toml Cargo.lock ./

# Si no hemos cacheado las dependencias (las 2 instrucciones anteriores)
RUN mkdir src

# Now copy the source code
COPY ./src ./src

# Build your application
RUN cargo build --release

#-------------------------------------------------------
# Start a new stage to create a smaller image without unnecessary 
# build dependencies
#-------------------------------------------------------
FROM debian:bookworm-slim

# Set the working directory
WORKDIR /usr/src/app

# Creamos usuario SIN privilegios
RUN addgroup --system webuser && adduser --system webuser

# Copy the built binary from the previous stage
COPY --from=builder /usr/src/app/target/release/holaMundo ./

# Usamos el usuario
USER webuser

# Command to run the application
CMD ["./holaMundo"]
