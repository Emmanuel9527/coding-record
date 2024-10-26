alpha = 0.05
df1 = 2
df2 = 2
f = qf(1-alpha,df1,df2)
print(f)

%5.10

%bear growth data
len_2 <- c(141,140,145,146,150,142,139)
len_3 <- c(157,168,162,159,158,140,171)
len_4 <- c(168,174,172,176,168,178,176)
len_5 <- c(183,170,177,171,175,189,175)
data <- cbind(len_2,len_3,len_4,len_5)

%安裝多變量package
install.packages("MVN")
library(MVN)


mean <- colMeans(data)
cov <- cov(data)
n <- nrow(data) 
p <- ncol(data) 

%T squared simultaneous CI

alpha <- 0.05
f_critical <- ((n - 1) * p / (n - p)) * qf(1 - alpha, p, n - p)
simultaneous_intervals <- matrix(0, nrow = p, ncol = 2)
colnames(simultaneous_intervals) <- c("Lower Bound", "Upper Bound")
rownames(simultaneous_intervals) <- colnames(data)
for (i in 1:p) {
  mean_col <- mean[i]
  se_col <- sqrt(cov[i, i] / n)
  lower_bdd <- mean_col - sqrt(f_critical) * se_col
  upper_bdd <- mean_col + sqrt(f_critical) * se_col
  simultaneous_intervals[i, ] <- c(lower_bdd, upper_bdd)
}

%計算年增量之T squared simultaneous CI

intervals_inc <- matrix(0, nrow = p-1, ncol = 2)
colnames(intervals_inc) <- c("Lower Bound", "Upper Bound")
rownames(intervals_inc) <- colnames(data)

for (i in 1:p-1) {
  mean_a <- mean[i+1]-mean[i]
  a <- matrix(c(0,0,0,0),nrow=1,ncol=4)
  a[1,i] <- -1
  a[1,i+1] <- 1
  se_a <- sqrt(a%*%cov%*%t(a) / n)
  lower_bdd <- mean_a - sqrt(f_critical) * se_a
  upper_bdd <- mean_a + sqrt(f_critical) * se_a
  intervals_inc[i, ] <- c(lower_bdd, upper_bdd)
}

%計算bonferroni interval
alpha <- 0.05
bonferroni_alpha <- alpha / ncol(data)

bonferroni_intervals <- matrix(0, nrow =2, ncol = ncol(data))

for (i in 1:ncol(data)) {
  mean_val <- mean(data[, i])
  se <- sd(data[, i]) / sqrt(nrow(data))
  lower_bound <- mean_val - qt(1 - bonferroni_alpha / 2, nrow(data) - 1) * se
  upper_bound <- mean_val + qt(1 - bonferroni_alpha / 2, nrow(data) - 1) * se
  bonferroni_intervals[ ,i] <- c(lower_bound, upper_bound)
}

%計算diff(mean)之bonferroni interval
mean_values <- colMeans(data)
diff_bonferroni_intervals <- matrix(0, nrow = 2, ncol = 3)

for (i in 1:(ncol(data) - 1)) {
  diff_vector <- data[, i + 1] - data[, i]
  se_diff <- sd(diff_vector) / sqrt(nrow(data))
  lower_bound <- mean(diff_vector) - qt(1 - alpha / 2, nrow(data) - 1) * se_diff
  upper_bound <- mean(diff_vector) + qt(1 - alpha / 2, nrow(data) - 1) * se_diff
  diff_bonferroni_intervals[1, i] <- lower_bound
  diff_bonferroni_intervals[2, i] <- upper_bound
}

%7.10
%(b)
alpha <-0.5
z <- matrix(c(-2,-1,0,1,2),nrow=5,ncol=1)
b1 <- matrix(c(3,0.9),nrow=2,ncol=1)
z_h <- matrix(c(1,0.5),nrow=2, ncol=1)
y1 <- matrix(c(5,3,4,2,1),nrow=5,ncol=1)
v <- var(y1)
t_z <- t(z)
t_z_z <- t_z %*% z
t_z_h <- t(z_h)
part1 <- (t_z_z)^-1*t_z_h %*% z_h
lower_bdd <- t_z_h %*% b1 - qt(1 - alpha / 2, nrow(z) - ncol(b1)) * sqrt(v * (1 + part1))
upper_bdd <- t_z_h %*% b1 + qt(1 - alpha / 2, nrow(z) - ncol(b1)) * sqrt(v * (1 + part1))




