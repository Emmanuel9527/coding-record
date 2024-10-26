toluca_data <- read.table(file = "C:/Users/User/Downloads/tolucadata.txt", 
header=FALSE, col.names = c('lotsize', 'workhrs'))
attach(toluca_data)
toluca_model <- lm(workhrs ~ lotsize)
b1_coef <- summary(toluca_model)$coef[2,1]
b1_std_error <- summary(toluca_model)$coef[2,2]

# 指定您想要檢定的值
null_value <- 3

# 計算 t 值
t_value <- (b1_coef - null_value) / b1_std_error

# 提取自由度
df <- summary(toluca_model)$df[2]

# 計算 p 值
p_value <- 2 * pt(-abs(t_value), df)

# 顯示 t 值和 p 值
print(paste("t-value:", t_value))
print(paste("p-value:", p_value))

#計算power
# 效應大小
effect_size <- 0.5

# 樣本大小
sample_size <- 25

# 標準差
b1_std_dev <- 0.35

# 顯著性水平
alpha <- 0.01

# 計算功效
power <- power.t.test(n = sample_size, delta = effect_size, sd = b1_std_dev, sig.level = alpha, type = "one.sample", alternative = "two.sided")

# 顯示功效
print(paste("功效:", power$power))
